<?php
/*
 * form.class.php
 *
 * Copyright 2011 gH0StArthour <ghostarthour@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */
/**
 * Create html forms with php
 * @author	gH0StArthour <ghostarthour@gmail.com>
 * @package htmlformgenerator
 */
class form {
    var $name;
    var $space = 10;
    var $f = array();
    var $formAttrs = array();
    var $opt_label = true;
    var $opt_id = 'auto';
    var $opt_legend;
    function __construct($name) {
        $this->name = $name;
    }
    /**
     * @desc	Add new input to the array
     * @param string $type	type of the input: (type="text|password|textarea|submit|reset|select etc...")
     * @param string $name	name of the input: (name="$name")
     * @param string $show	String in the label
     * @param mixed $value	the value of the input, in case of select, must in array
     * @param mixed $id	the id of the input, when the label is enabled, then the label "for" attibute will the id: <label for=$id>
     * @param bool $label	label on/off
     * @param mixed $pclass	class of the p element: class="$pclass'
     * @return int	The id of the created input (internal id, int)
     */
    public function addInput($type, $name, $show, $value = '', $id = 0, $label = true, $pclass = null) {
        if ($this->opt_id == 'auto') {
            $id = count($this->f);
        }
        $this->f[$id] = array(
            'pclass' => $pclass,
            'show' => $show,
            'type' => $type,
            'name' => $name,
            'value' => $this->__typeConversion($value),
            'label' => $label,
            'attributes' => array()
        );
        return $id;
    }
    /**
     * @desc	type conversation
     * @param mixed $t	the value, will be detect the type
     * @return mixed	return the really typed value
     */
   static function __typeConversion($t) {
        if (is_numeric($t)) {
            return intval($t);
        }
        if (is_string($t)) {
            return $t;
        }
        if (is_bool($t)) {
            return (bool) $t;
        }
        return $t;
    }
    /**
     * @desc	add attribute to the field
     * @param mixed $id	the id, what will be modified
     * @param mixed $a	on more attribute, it must associated array, other case is must string
     * @param mixed $b	whet the second parameter is not an array, then this must add this as string
     * @return
     */
    public function addAttr($id, $a, $b = null) {
        if (is_array($a) AND $b == null) {
            $this->f[$id]['attributes'] = array_merge($this->f[$id]['attributes'], $a);
        } else {
            $this->f[$id]['attributes'][$a] = $b;
        }
    }
    /**
     * @desc	delete one or more attribute from an input
     * @param mixed $id	the internal id of the input
     * @param string $n	the name of the attribute
     * @param bool $setnull	on false: delete the attribute, on true will set null as value
     * @return
     */
    public function removeAttr($id, $n, $setnull = false) {
        if ($setnull === false) {
            unset($this->f[$id]['attributes'][$n]);
        } else {
            $this->f[$id]['attributes'][$n] = null;
        }
    }
    /**
     * @desc	add attribute to the form tag
     * @param mixed $a	on more attribute, it must associated array, other case is must string
     * @param mixed $b	whet the second parameter is not an array, then this must add this as string
     * @return
     */
    public function addFormAttr($a,$b=null) {
	    if (is_array($a) AND $b == null) {
            $this->formAttrs = array_merge($this->formAttrs, $a);
        } else {
            $this->formAttrs[$a] = $b;
        }
	}
    /**
     * @desc	render a form
     * @param mixed $id	the internal id of the input... when it is null, then all will be shown
     * @return mixed
     */
    public function show($id = null) {
        $render = "\n".$this->indent("form")."<form method='post' name='" . $this->name . "'";
        $this->addFormAttr("action",$_SERVER["REQUEST_URI"]);
        foreach ($this->formAttrs as $fak => $fav) {
			$render.=' '.$fak.'="'.$fav.'" ';
		}
        $render.=">\n".$this->indent("fieldset")."<fieldset>";
        if (!empty($this->opt_legend)) {
			$render.= $this->indent("legend")."<legend>".$this->opt_legend."</legend>\n";
		}
        if ($id != null) {
            return $this->render($id);
        } else {
            if ($this->opt_id == 'auto') {
                for ($z = 0; $z < count($this->f); $z++) {
                    $render .= $this->render($z);
                }
            }/* else {
                foreach ($this->f as $k => $v) {
                    //TODO: continue...
                }
            }*/
        }
        $render .= "\n".$this->indent("fieldset")."</fieldset>\n".$this->indent("form")."</form>\n";
        return $render;
    }
    /**
     * @desc	create a button
     * @param string $name	the name of the button
     * @param string $value	the value of the button
     * @param string $type	type of the button (submit/reset ...)
     * @param mixed $pclass	class of the p tag
     * @return mixed
     */
    public function addButton($name, $value, $type = 'submit', $pclass = null) {
        return $this->addInput($type, $name, null, $value, '', false, $pclass);
    }
    public function addSelect($name,$value,$show) {
		return $this->addInput("select", $name, $show, $value);
	}
	private function repeate($i) {
		return str_repeat(" ",$this->space*$i);
	}
	private function indent($type) {
		switch ($type) {
			case "p":
				return $this->repeate(1.5);
			break;
			case "fieldset":
				return $this->repeate(1);
			break;
			case "label":
				return $this->repeate(1.8);
			break;
			case "input":
				return $this->repeate(1.8);
			break;
			case "form":
				return $this->repeate(.5);
			break;
		}
	}
    /**
     * @desc	egy form megjelenítése
     * @param mixed $id	az input mező id-je, amelyet megszeretnénk jeleníteni
     * @return mixed
     */
    private function render($id) {
        $form = "\n".$this->indent("p")."<p id=\"p_".$this->f[$id]['name'].'_' . $id."\"" . ($this->f[$id]['pclass'] == null ? '' : ' class="' . $this->f[$id]['pclass'] . '"') . ">";
        $form.="\n";
        if ($this->opt_label === true AND $this->f[$id]['label'] === true) {
            $form .= $this->indent("label").'<label for="'.$this->f[$id]['name'].'_' . $id . '">' . $this->f[$id]['show'] . '</label>';
            $form .="\n";
        }
        switch ($this->f[$id]['type']) {
            case 'email':
				$form .= $this->indent("input");
                $form .= '<input type="' . $this->f[$id]['type'] . '"';
                $form .=' name="' . $this->f[$id]['name'] . '"';
                $form .=' value="' . $this->f[$id]['value'] . '"';
				$form .=' id="'.$this->f[$id]['name'].'_' . $id . '"';
                foreach ($this->f[$id]['attributes'] as $k => $v) {
                    $form .= " ".$k . '="' . $v . '"';
                }
                $form .= '/>';
                break;
            case 'submit':
            case 'reset':
            case 'password':
            case 'text':
            case 'radio':
            case 'checkbox':
            case 'file':
				$form .= $this->indent("input");
                $form .= '<input';
				$form .= ' type="' . $this->f[$id]['type'] . '"';
				$form .= ' name="' . $this->f[$id]['name'] . '"';
				$form .= ' value="' . $this->f[$id]['value'] . '"';
				$form .= ' id="'.$this->f[$id]['name'].'_' . $id . '"';
                foreach ($this->f[$id]['attributes'] as $k => $v) {
                    $form .= " ".$k . '="' . $v . '"';
                }
                $form .= '/>';
                break;
            case 'textarea':
				$form .= $this->indent("input");
                $form .= '<textarea ';
				$form .='name="' . $this->f[$id]['name'] . '" ';
				$form .='id="'.$this->f[$id]['name'].'_' . $id . '" ';
                foreach ($this->f[$id]['attributes'] as $k => $v) {
                    $form .= $k . '="' . $v . '"';
                }
                $form .= '>' . $this->f[$id]['value'] . '</textarea>';
                break;
            case 'select':
				$form .= $this->indent("select");
                $form .= '<select name="' . $this->f[$id]['name'] . '"';
                $form .='id="'.$this->f[$id]['name'].'_' . $id . '"';
                foreach ($this->f[$id]['attributes'] as $k => $v) {
                    if ($k != 'selected')
                        $form .= " ".$k . '="' . $v . '"';
                }
                $form .= ">";
                $selected = false;
                if (isset($this->f[$id]['attributes']['selected'])) {
                    $selected = $this->f[$id]['attributes']['selected'];
                }
                unset($this->f[$id]['attributes']['selected']);
                $i = 0;
                foreach ($this->f[$id]['value'] as $k => $v) {
                    $default = '';
                    if ($i === $selected OR $selected == $k) {
                        $default = 'selected="selected"';
                    }
                    $form .= $this->indent("option");
                    $form .= '<option value="' . $k . '" ' . $default . ' >' . $v . '</option>';
                    $form .= "\n";
                    $i++;
                }
                $form .= '</select>';
                $form .="\n";
                break;
        }
        if (isset($this->f[$id]['error'])) {
            $form .= '<span>';
            $form .= $this->f[$id]['error'];
            $form .= '</span>';
        }
        $form .= "\n".$this->indent("p").'</p>';
        $form .="\n";
        return $form;
    }
    function __destroy() {
        unset($this->f);
    }
}
