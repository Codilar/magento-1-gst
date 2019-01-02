<?php 

class Codilar_Gst_Block_Adminhtml_Catalog_Category_Tab_GstPriceRules extends Mage_Adminhtml_Block_Template{

	const FORM_ELEMENT_ARRAY_NAME = 'codilar_gst';


	protected $_columns = array();
	protected $_previousValues = array();

	protected function _prepareLayout(){
		$this->setTemplate('gst/catalog/category/tab/price_rules.phtml');
		$this->addColumn('price_min', array(
			'label'	=>	"Price Min",
			'type'	=>	"text",
			'class'	=>	"input-text required-entry"
		));
		$this->addColumn('price_max', array(
			'label'	=>	"Price Max",
			'type'	=>	"text",
			'class'	=>	"input-text required-entry"
		));
		$this->addColumn('tax_class', array(
			'label'		=>	"Tax Class",
			'type'		=>	"select",
			'class'		=>	"input-text required-entry",
			'values'	=>	$this->getTaxClasses()
		));
		$category = Mage::registry('current_category');
		try{
			$priceRules = Mage::helper('core')->jsonDecode($category->getGstPriceRules());
		}
		catch(Exception $e){
			$priceRules = [];
		}
		$this->setPreviousValues($priceRules);
	}

	public function getTaxClasses(){
		$collection = Mage::getModel('tax/class')->getCollection();
		$values = [];
		foreach($collection as $item){
			$values[] = array(
				'label'	=>	$item->getClassName(),
				'value'	=>	$item->getClassId()
			);
		}
		return $values;
	}

	protected function setPreviousValues($values = []){
		$this->_previousValues = $values;
	}

	public function getPreviousValuesHtml(){
		$values = $this->_previousValues;
		$columns = array_keys($values);
		if(count($columns) <= 0) return;
		$html = '';
		foreach($values[$columns[0]] as $key => $value) {
			$data = array();
			foreach($columns as $column){
				$data[$column] = $values[$column][$key];
			}
			$html .= '<tr>'.$this->getRowHtml($data).'</tr>';
		}
		return $html;
	}

	protected function _getColumnRequiredValues(){
		return array(
			'label', 'type', 'class'
		);
	}

	protected function addColumn($name, $params = []){
		foreach($this->_getColumnRequiredValues() as $columnRequiredValue){
			if(!isset($params[$columnRequiredValue])) $params[$columnRequiredValue] = "";
		}
		$this->_columns[$name] = $params;
		return $this;
	}

	public function getColumns(){
		return $this->_columns;
	}

	public function getFormElementArrayName(){
		return self::FORM_ELEMENT_ARRAY_NAME;
	}

	public function getRowHtml($values = []){
		$html = '';
		foreach ($this->getColumns() as $name => $column) {
			$valuePresent = array_key_exists($name, $values);
			switch($column['type']){
				case "text":
					$html .= '<td><input type="'.$column['type'].'" name="'.$this->getFormElementArrayName()."[$name][]".'" class="'.$column['class'].'" value="';
					if($valuePresent){
						$html .= $values[$name];
					}
					$html .= '" /></td>';
					break;
				case "select":
					$html .= '<td><select name="'.$this->getFormElementArrayName()."[$name][]".'" class="'.$column['class'].'">';
					$html .= '<option value="">Choose '.ucwords($column['label']).'</option>';
					if(isset($column['values'])){
						foreach($column['values'] as $value){
							if($valuePresent && $values[$name] == $value['value']){
								$selectedHtml = 'selected="selected"';
							}
							else{
								$selectedHtml = "";
							}
							$html .= '<option value="'.$value['value'].'" '.$selectedHtml.'>'.$value['label'].'</option>';
						}
					}
					$html .= "</select></td>";
					break;
			}
		}
		$html .= '<td><button class="btn btn-danger" onclick="javascript:Codilar_Gst.deleteItem(this);return false;">Delete</button></td>';
		return $html;
	}

	public function getLabel(){
		return "SET TAX RULES FOR CATEGORY";
	}
}