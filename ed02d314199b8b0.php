<?php

	/*

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

	--------------------------------------------------------------------

	Designed by Bazinga Designs London / Warsaw
        http://www.bazingadesigns.com/en

	Open-source/free code, libraries and sites that we either used to create
        Kinky Slider or we got inspiration from - thank you guys!

        The amazing SFMoviePosterBold font (free for personal use)
        http://www.dafont.com/sf-movie-poster.font

        attention: we're unsure whether you can use this font in your projects
        if they are of commercial nature. from what we understood we could use it for a
        non-commercial software that Kinky Slider is but commercial applications
        of Kinky Slider with this font included may be against the law. if unsure
        please just change the font. apart from that, you can do with Kinky Slider
        whatever you want.

        The nuff preloader gif generated using
        http://www.ajaxload.info/

        jQuery Timers Plugin
        http://jquery.offput.ca/every/

        jQuery 2d Transform Plugin:
        github.com/heygrady/transform/wiki

	Presta Shop Module Turorial - one and only
	http://www.ecartservice.net/20072009/writing-your-own-prestashop-module-part-1/


	Directory2Array function by XoloX
	http://snippets.dzone.com/posts/show/155


	TimThumb (the ZoomCrop algorithm)
	http://www.binarymoon.co.uk/projects/timthumb/


	*/

define('KINKYSLIDER_DEFAULT_TABLE_NAME','kinkyslider');
define('KINKYSLIDER_DEFAULT_CONFIG_TABLE_NAME','kinkyslider_config');
define('KINKYSLIDER_DEFAULT_FILE_NAME','kinkyslider');

define('KINKYSLIDER_VALIDATION_STANDARD','1');
define('KINKYSLIDER_VALIDATION_NUMERIC','2');
define('KINKYSLIDER_VALIDATION_NUMERIC_OR_NONE','3');
define('KINKYSLIDER_VALIDATION_NUMERIC_OR_AUTO','4');
define('KINKYSLIDER_VALIDATION_URL','6');

define('KINKYSLIDER_CONFIG_CAT_GENERAL','1');
define('KINKYSLIDER_CONFIG_CAT_HEADER','2');
define('KINKYSLIDER_CONFIG_CAT_PRICE','3');



class ed02d314199b8b0 extends Module {

    private $_html = '';                                            // used to store the html output for the back-office
    private $_postErrors = array();                                 // used to store and display any errors that may appear during form submission in the back-office

    private $kinkyslider_image;                                     // will hold the image currently resized and put into the database
    private $kinkyslider_image_type;                                // will hold the image type of the currently resized image (JPG/PNG/GIF)
    private $kinkyslider_imgcount;                                  // will hold the total number of slides - set in the back-office

    private $kinkyslider_calculated_image_width;
    private $kinkyslider_calculated_image_height;

    private $kinkyslider_output_images = array();                   // will be filled with the slides data to display both on the main page and in the back-office.
    private $kinkyslider_config = array();                          // key=>value
    private $kinkyConfigurations=array(
                    KINKYSLIDER_CONFIG_CAT_GENERAL=>'General settings',
                    KINKYSLIDER_CONFIG_CAT_HEADER=>'Headers settings',
                    KINKYSLIDER_CONFIG_CAT_PRICE=>'Price tags settings',
                    );



    function __construct() {

        $version_mask = explode('.', _PS_VERSION_, 3);                          // let's check the PrestaShop version here
        $version_test = $version_mask[0] > 0 && $version_mask[1] > 3;
        $this->name = 'ed02d314199b8b0';
        $this->tab = $version_test ? 'slideshows' : 'Galeria Koloru modules';  // if the version is 1.4 or higher we will put the module under 'slideshows' tab, otherwise we'll create our own tab named 'Bazinga Designs modules'
        if ($version_test) $this->author = 'Jelocartel';         // if the version is 1.4 or higher we can set the author for the module
        $this->version = '0.1.0';                                               // here we set the version of our module
        parent::__construct();
        $this->displayName = $this->l('Lista Producentow');                  // We set 'Bazinga Kinky Slider' as the module's name
        $this->description = $this->l('Lista producentow w stopce');

        $this->_getSettings();
        $this->_getSlideList();

    }


    public function install() {

        if (!parent::install()) return false;
        if (!$this->registerHook('footer')) return false;
        if (!$this->_createKinkyTable()) return false;
        if (!$this->_createKinkyConfigTable()) return false;
        if (!$this->_insertStartUpSlides()) return false;
        return true;

    }

    public function uninstall() {

        if (!parent::uninstall()) return false;

        Db::getInstance()->Execute
                (
                    "DROP TABLE `".KINKYSLIDER_DEFAULT_CONFIG_TABLE_NAME."`"
                );

        Db::getInstance()->Execute
                (
                    "DROP TABLE `".KINKYSLIDER_DEFAULT_TABLE_NAME."`"
                );

        return true;

    }

    private function _getSettings() {

        $kinkyQuery='SELECT * FROM '.KINKYSLIDER_DEFAULT_CONFIG_TABLE_NAME;
        if ($kinkyResults = Db::getInstance()->ExecuteS($kinkyQuery)) {

            $this->kinkyslider_config=$kinkyResults;

        }

        $this->kinkyslider_calculated_image_width=(int)$this->_getConfigValueByKey('width') - ((int)$this->_getConfigValueByKey('borderWidth'))*2;
        $this->kinkyslider_calculated_image_height=(int)$this->_getConfigValueByKey('height') - ((int)$this->_getConfigValueByKey('borderWidth'))*2;

    }

    private function _getConfigValueByKey($kinkyKey) {

        $kinkyID=$this->_multidimensionalSearch($this->kinkyslider_config, array('kinky_key'=>$kinkyKey));
        return ($this->kinkyslider_config[$kinkyID]['kinky_value']);
    }

    private function _setConfigValue($kinkyID, $kinkyValue) {

        Db::getInstance()->Execute
                (
                    "UPDATE ".KINKYSLIDER_DEFAULT_CONFIG_TABLE_NAME." SET kinky_value='$kinkyValue' WHERE kinky_id=$kinkyID"
                );

    }


    private function _createKinkyTable()
    {
        if (!Db::getInstance()->Execute
            (
                    'CREATE TABLE `'.KINKYSLIDER_DEFAULT_TABLE_NAME.'` (
                    `kinky_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `kinky_link` varchar(255) NOT NULL ,
                    `kinky_imagedir` varchar(10) NOT NULL ,
                    `kinky_header` varchar (255) NOT NULL ,
                    `kinky_price` varchar (255) NOT NULL ,
                    `kinky_order` INT UNSIGNED NOT NULL ,
                    `kinky_active` BOOLEAN

                    ) ENGINE=MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
            )) return false;

        return true;
    }

    private function _createKinkyConfigTable()
    {

        if (!Db::getInstance()->Execute
            (
                    'CREATE TABLE `'.KINKYSLIDER_DEFAULT_CONFIG_TABLE_NAME.'` (
                    `kinky_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `kinky_key` varchar(100) NOT NULL ,
                    `kinky_value` varchar(255) NOT NULL ,
                    `kinky_validation` varchar (1) NOT NULL ,
                    `kinky_category` varchar (1) NOT NULL ,
                    `kinky_label` varchar(255) NOT NULL

                    ) ENGINE=MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;'
            )) return false;

        if (!Db::getInstance()->Execute
                (
                    "INSERT INTO `".KINKYSLIDER_DEFAULT_CONFIG_TABLE_NAME."` ".
                    "(kinky_key,kinky_value,kinky_validation,kinky_category,kinky_label) ".
                    "VALUES ".
                    "( 'speed' , '3000' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Delay between slides'),".
                    "( 'slideChangeSpeed' , '1000' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Slide change speed'),".
                    "( 'width' , '556' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Slider width'),".
                    "( 'height' , '336' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Slider height'),".
                    "( 'borderWidth' , '5' , '".KINKYSLIDER_VALIDATION_NUMERIC_OR_NONE."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Border width'),".
                    "( 'borderColor' , 'EEEEEE' , '".KINKYSLIDER_VALIDATION_STANDARD."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Border color'),".
                    "( 'marginTop' , '0' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Top margin'),".
                    "( 'marginRight' , 'auto' , '".KINKYSLIDER_VALIDATION_NUMERIC_OR_AUTO."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Right margin'),".
                    "( 'marginBottom' , '20' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Bottom margin'),".
                    "( 'marginLeft' , 'auto' , '".KINKYSLIDER_VALIDATION_NUMERIC_OR_AUTO."' , '".KINKYSLIDER_CONFIG_CAT_GENERAL."' , 'Left margin'),".
                    "( 'headerOutSwingSpeed' , '800' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Header out swing speed'),".
                    "( 'headerRotateSpeed' , '800' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Header rotation speed'),".
                    "( 'headerHeight' , '35' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Header height'),".
                    "( 'headerPaddingTop' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Top padding'),".
                    "( 'headerPaddingRight' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Right padding'),".
                    "( 'headerPaddingBottom' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Bottom padding'),".
                    "( 'headerPaddingLeft' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Left padding'),".
                    "( 'headerMarginLeft' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Left shift'),".
                    "( 'headerMarginBottom' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Bottom shift'),".
                    "( 'headerBackground' , 'FAFAFA' , '".KINKYSLIDER_VALIDATION_STANDARD."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Background'),".
                    "( 'headerFont' , 'SFMoviePosterBold' , '".KINKYSLIDER_VALIDATION_STANDARD."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Font family'),".
                    "( 'headerFontSize' , '35' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Font size'),".
                    "( 'headerFontColor' , '363636' , '".KINKYSLIDER_VALIDATION_STANDARD."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Font color'),".
                    "( 'headerLineHeight' , '35' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_HEADER."' , 'Line height'),".
                    "( 'priceAppearSpeed' , '1000' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Price appear speed'),".
                    "( 'priceDisappearSpeed' , '800' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Price disappear speed'),".
                    "( 'pricePaddingTop' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Top padding'),".
                    "( 'pricePaddingRight' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Right padding'),".
                    "( 'pricePaddingBottom' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Bottom padding'),".
                    "( 'pricePaddingLeft' , '10' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Left padding'),".
                    "( 'priceBackground' , 'FE57A1' , '".KINKYSLIDER_VALIDATION_STANDARD."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Background'),".
                    "( 'priceFont' , 'Verdana' , '".KINKYSLIDER_VALIDATION_STANDARD."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Font family'),".
                    "( 'priceFontSize' , '15' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Font size'),".
                    "( 'priceFontColor' , 'FAFAFA' , '".KINKYSLIDER_VALIDATION_STANDARD."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Font color'),".
                    "( 'priceLineHeight' , '35' , '".KINKYSLIDER_VALIDATION_NUMERIC."' , '".KINKYSLIDER_CONFIG_CAT_PRICE."' , 'Line height')"

                )) return false;

        return true;
    }

    private function _insertStartUpSlides() {

        if(!Db::getInstance()->Execute
                (
                    "INSERT INTO `".KINKYSLIDER_DEFAULT_TABLE_NAME."` ".
                    "(kinky_link,kinky_imagedir,kinky_header,kinky_price,kinky_order,kinky_active) ".
                    "VALUES ".
                    "( 'http://www.bazingadesigns.com/en' , '1' , 'bazingadesigns.com' , 'trusted designers' , 1, 1),".
                    "( 'http://www.bazingadesigns.com/en' , '2' , 'web design' , 'from ₤599' , 2, 1),".
                    "( 'http://www.bazingadesigns.com/en' , '3' , 'on-line stores ' , 'from ₤799' , 3, 1),".
                    "( 'http://www.bazingadesigns.com/en' , '4' , 'logo design' , 'from ₤399' , 4, 1),".
                    "( 'http://www.bazingadesigns.com/en' , '5' , 'blog design' , 'from ₤599' , 5, 1)"

                )) return false;

        return true;

    }

    private function _createNewSlide() {

        $kinkyOrder=$this->_getLastSlideInDB();

        if (!empty($kinkyOrder)) {

            if (is_numeric($kinkyOrder)) {
                $kinkyOrder++;
            } else {
                $kinkyOrder=1;
            }

       } else {

            $kinkyOrder=1;
       }


        Db::getInstance()->Execute
                (
                    "INSERT INTO `".KINKYSLIDER_DEFAULT_TABLE_NAME."` ".
                    "(kinky_link,kinky_imagedir,kinky_header,kinky_price,kinky_order,kinky_active) ".
                    "VALUES ('', '', '', '', ".$kinkyOrder.", 0)"
                );

    }


    private function _updataSlide($slideID, $slideLink='', $slideImageDir='', $slideHeader='', $slidePrice='', $slideOrder=0, $slideActive=0) {

        if (!empty($slideID)) {

            if (is_numeric($slideID)) {

                Db::getInstance()->Execute
                        (
                            "UPDATE ".KINKYSLIDER_DEFAULT_TABLE_NAME." SET kinky_link='$slideLink', kinky_imagedir='$slideImageDir', kinky_header='$slideHeader', kinky_price='$slidePrice', kinky_order=$slideOrder, kinky_active=$slideActive WHERE kinky_id=$slideID"
                        );
            }
        }


    }

    private function _getLastSlideInDB() {

        $kinkyQuery='SELECT MAX(kinky_order) as lastSlideOrder FROM '.KINKYSLIDER_DEFAULT_TABLE_NAME;
        $kinkyResult=Db::getInstance()->getRow($kinkyQuery);
        return $kinkyResult['lastSlideOrder'];

    }

    private function _deleteSlide($slideID) {

        if (!empty($slideID)) {

            if (is_numeric($slideID)) {

                $kinkyQuery="DELETE FROM ".KINKYSLIDER_DEFAULT_TABLE_NAME." WHERE kinky_id=$slideID";
                if ($kinkyResults = Db::getInstance()->ExecuteS($kinkyQuery)) {

                    return $kinkyResults;

                }
            }
        }

        return false;
    }

    private function _getNumberOfSlides() {

        $kinkyQuery='SELECT COUNT(*) as numberOfSlides FROM '.KINKYSLIDER_DEFAULT_TABLE_NAME;
        $kinkyResult=Db::getInstance()->getRow($kinkyQuery);
        return $kinkyResult['numberOfSlides'];

    }

    private function _getSlideList() {

        $kinkyQuery='SELECT * FROM '.KINKYSLIDER_DEFAULT_TABLE_NAME.' ORDER BY kinky_order';
        if ($kinkyResults = Db::getInstance()->ExecuteS($kinkyQuery)) {

            $this->kinkyslider_output_images=$kinkyResults;

        }

    }

    private function _linkValidation($linkString) {

        if (!Validate::isAbsoluteUrl($linkString)) {
            $this->_postErrors[] = $this->l('Invalid URL: "'.strip_tags(nl2br2($linkString)).'". Hint: start with http://');
        }
    }

    private function _numericValidation($value, $fieldName) {

            if (!Validate::isUnsignedInt($value))
                    $this->_postErrors[] = $this->l('Incorrect value for the field: ').' '.$fieldName.' '.$this->l(' - must be integer.');
    }

    private function _numericOrAutoValidation($value, $fieldName) {
            if (strtolower($value)!='auto'):
            if (!Validate::isUnsignedInt($value))
                    $this->_postErrors[] = $this->l('Incorrect value for the field: ').' '.$fieldName.' '.$this->l(' - must be integer or "auto".');
            endif;
    }

    private function _numericOrNoneValidation($value, $fieldName) {

            if (strtolower($value)!='none'):
            if (!Validate::isUnsignedInt($value))
                    $this->_postErrors[] = $this->l('Incorrect value for the field: ').' '.$fieldName.' '.$this->l(' - must be integer or "none".');
            endif;
    }


    public function getContent() {


        if (Tools::isSubmit('addNewSlide')) {

            $this->_createNewSlide();

        }

        foreach ($this->kinkyslider_output_images as $slideToUpdate) {

            if (Tools::isSubmit($this->name.'_deleteslide_'.$slideToUpdate['kinky_id'])) {

                $this->_deleteSlide($slideToUpdate['kinky_id']);
            }
        }


        if (Tools::isSubmit('updateSlides')) {

            foreach ($this->kinkyslider_output_images as $slideToUpdate) {

                $_thisID=$slideToUpdate['kinky_id'];
                $_thisLink=$slideToUpdate['kinky_link'];
                $_thisImageDir=$slideToUpdate['kinky_imagedir'];
                $_thisHeader=$slideToUpdate['kinky_header'];
                $_thisPrice=$slideToUpdate['kinky_price'];
                $_thisOrder=$slideToUpdate['kinky_order'];
                $_thisActive=$slideToUpdate['kinky_active'];

                $_thisSlideIsChanged=false;

                /* Let's see if the user wanted to upload an image for this slide ID
                 * If so, we'll create a new directory on the server, move the uploaded image there,
                 * Scale the image to the width & height given in the configuration and
                 * Then save it as JPEG image.
                 */

                if (isset($_FILES[$this->name.'_image'.$_thisID]) AND isset($_FILES[$this->name.'_image'.$_thisID]['tmp_name']) AND !empty($_FILES[$this->name.'_image'.$_thisID]['tmp_name'])) {

                        if ($error = @checkImage($_FILES[$this->name.'_image'.$_thisID], 4000000)) {
                                        $this->_postErrors[]= $error;
                                } else

                                {

                                        $_last_picture_dir=$this->bazinga_last_dir('images');
                                        $_numeric_last_picture_dir=(int)$_last_picture_dir;

                                        $_new_picture_dir=$_numeric_last_picture_dir+1;
                                        $_target_path=dirname(__FILE__).'/uploads/images/'.$_new_picture_dir.'/';

                                        mkdir(str_replace('//','/',$_target_path), 0755, true);

                                        if (move_uploaded_file($_FILES[$this->name.'_image'.$_thisID]['tmp_name'],$_target_path.KINKYSLIDER_DEFAULT_FILE_NAME.'.png')) {

                                                // $this->bazinga_load($_target_path.$_FILES[$this->name.'_image'.$_thisID]['name']);
                                                // $this->bazinga_resizeZoomCrop($this->kinkyslider_calculated_image_width,$this->kinkyslider_calculated_image_height);
                                                //$this->bazinga_save($_target_path.KINKYSLIDER_DEFAULT_FILE_NAME.'.png');

                                                $_thisImageDir=$_new_picture_dir;
                                                $_thisSlideIsChanged=true;
                                        }
                                }

                }




                if ($_checkUpdate = Tools::getValue($this->name.'_link'.$_thisID)) {

                    $this->_linkValidation($_checkUpdate);

                    if (!sizeof($this->_postErrors)) {

                            $_thisLink=$_checkUpdate;
                            $_thisSlideIsChanged=true;
                    }

                }

                if ($_checkUpdate = Tools::getValue($this->name.'_order'.$_thisID)) {

                    $this->_numericValidation($_checkUpdate, 'order');

                    if (!sizeof($this->_postErrors)) {

                            $_thisOrder=$_checkUpdate;
                            $_thisSlideIsChanged=true;
                    }

                }

                if ($_checkUpdate = Tools::getValue($this->name.'_header'.$_thisID)) {

                    $_thisHeader=strip_tags(nl2br2($_checkUpdate));
                    $_thisSlideIsChanged=true;

                }

                if ($_checkUpdate = Tools::getValue($this->name.'_price'.$_thisID)) {

                    $_thisPrice=strip_tags(nl2br2($_checkUpdate));
                    $_thisSlideIsChanged=true;

                }

                if ($_checkUpdate = (int)Tools::getValue($this->name.'_active'.$_thisID)) {

                    if ($_checkUpdate==1 && $_thisActive==0) {
                        $_thisActive=1;
                        $_thisSlideIsChanged=true;

                    }

                } elseif ( ((int)Tools::getValue($this->name.'_active'.$_thisID)!=1) && $_thisActive==1) {

                        $_thisActive=0;
                        $_thisSlideIsChanged=true;
                }

                if ($_thisSlideIsChanged==true) {

                    $this->_updataSlide($_thisID, $_thisLink, $_thisImageDir, $_thisHeader, $_thisPrice, $_thisOrder, $_thisActive);
                }

            } /* end foreach */

                    if (!sizeof($this->_postErrors)) {

                            $this->_html .= '<div class="conf confirm">'.$this->l('Ustawienia zostały zaktualizowane').'</div>';
                    }

                    else {
                            foreach ($this->_postErrors AS $err) {
                                    $this->_html .= '<div class="alert error">'.$err.'</div>';
                            }
                    }

        } /*end isSubmit('updateSlider'); */


            if (Tools::isSubmit('updateSettings'))
            {


                foreach ($this->kinkyslider_config as $configRowToUpdate) {

                    if ($_checkUpdate = Tools::getValue($this->name.'_config_'.$configRowToUpdate['kinky_key'])) {

                    switch ($configRowToUpdate['kinky_validation']) {

                        case KINKYSLIDER_VALIDATION_STANDARD:
                        break;

                        case KINKYSLIDER_VALIDATION_NUMERIC:
                            $this->_numericValidation($_checkUpdate, $configRowToUpdate['sml_key']);
                        break;

                        case KINKYSLIDER_VALIDATION_NUMERIC_OR_NONE:
                            $this->_numericOrNoneValidation($_checkUpdate, $configRowToUpdate['kinky_label']);
                        break;

                        case KINKYSLIDER_VALIDATION_NUMERIC_OR_AUTO:
                            $this->_numericOrAutoValidation($_checkUpdate, $configRowToUpdate['kinky_label']);
                        break;

                    }

                    if (!sizeof($this->_postErrors)) {

                        $_thisNewValue=strip_tags(nl2br2($_checkUpdate));
                        $this->_setConfigValue($configRowToUpdate['kinky_id'], $_thisNewValue);

                    }

                    } elseif (Tools::getValue($this->name.'_config_'.$configRowToUpdate['kinky_key'])==0) {

                        $_thisNewValue=0;
                        $this->_setConfigValue($configRowToUpdate['kinky_id'], $_thisNewValue);

                    }

                }

                if (!sizeof($this->_postErrors)) {

                        $this->_html .= '<div class="conf confirm">Your settings have been saved.</div>';
                }

                else {
                        foreach ($this->_postErrors AS $err) {
                                $this->_html .= '<div class="alert error">'.$err.'</div>';
                        }
                }

            }

            $this->_getSettings();
            $this->_getSlideList();
            $this->_displayForm();

            return $this->_html;

    }

        private function _displayForm()
        {


                $this->_html .= '<br />';
                $this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">';
                $this->_html .= '<fieldset>';

                $this->_html .= '<br /><br />';
                $this->_html .= '<legend>'.$this->l('Manage slides').'</legend>';
                $this->_html .= '<input type="submit" name="addNewSlide" value="'.$this->l('Add new slide ✚').'" class="button" />';
                $this->_html .= ' &nbsp ';
                $this->_html .= '<input type="submit" name="updateSlides" value="'.$this->l('Update slides ✈').'" class="button" />';
                $this->_html .= '<br /><br />';
                $this->_html .= 'Ideal image size: '.$this->kinkyslider_calculated_image_width.'px X '.$this->kinkyslider_calculated_image_height.'px';
                $this->_html .= '<br />';


                foreach ($this->kinkyslider_output_images as $singleSliderImage) {


                    $_thisID=$singleSliderImage['kinky_id'];
                    $_thisLink=$singleSliderImage['kinky_link'];
                    $_thisImageDir=$singleSliderImage['kinky_imagedir'];
                    $_thisHeader=$singleSliderImage['kinky_header'];
                    $_thisPrice=$singleSliderImage['kinky_price'];
                    $_thisOrder=$singleSliderImage['kinky_order'];
                    $_thisActive=$singleSliderImage['kinky_active'];

                    $_isCheckedString=($_thisActive==1)?'checked="checked"':'';


                    $this->_html.='<hr><hr>';
                    $this->_html.='<br />';

                    $this->_html.='<div style="float:left; width:300px;">';

                    $this->_html.='<label>Order of appearance</label>';
                    $this->_html.='<div class="margin-form">';
                    $this->_html.='<input type="text" name="'.$this->name.'_order'.$_thisID.'" value="'.$_thisOrder.'"  style="width:200px" >';
                    $this->_html.='</div>';

                    $this->_html.='<label>Upload image:</label>';
                    $this->_html.='<div class="margin-form">';
                    $this->_html.='<input type="file" name="'.$this->name.'_image'.$_thisID.'"  style="width:200px"  />';
                    $this->_html.='</div>';

                    $this->_html.='<label>Link to:</label>';
                    $this->_html.='<div class="margin-form">';
                    $this->_html.='<input type="text" name="'.$this->name.'_link'.$_thisID.'" value="'.$_thisLink.'" style="width:200px" />';
                    $this->_html.='</div>';

                    $this->_html.='<label>Show on the page (active) ?</label>';
                    $this->_html.='<div class="margin-form">';
                    $this->_html.='<input type="checkbox" name="'.$this->name.'_active'.$_thisID.'" value="1" '.$_isCheckedString.'>';
                    $this->_html.='</div>';


                    $this->_html.='</div>';
                    $this->_html.='<div style="float:right; width:300px;">';

                    if (($_thisImageDir!=null) || ($_thisImageDir!='')) {

                            $this->_html .= '<img src="'._MODULE_DIR_.$this->name.'/uploads/images/'.$_thisImageDir.'/'.KINKYSLIDER_DEFAULT_FILE_NAME.'.png" width="300" />';

                    } else

                    {
                            $this->_html .= '<h3>'.$this->l('Image for this slide has not been loaded yet.').'</h3>';
                    }

                    $this->_html.='<div style="margin-top:5px; text-align:right;" >';
                    $this->_html.='<input type="submit" name="'.$this->name.'_deleteslide_'.$_thisID.'" class="button" value="Remove this slide" />';
                    $this->_html.='</div>';

                    $this->_html.='</div>';
                    $this->_html.='<div style="clear:both"></div>';
                    $this->_html.='<br />';


                }

                $this->_html .= '</fieldset>';
                $this->_html .= '</form>';

              




        }

    function hookFooter($params) {

        global $smarty, $protocol_content, $server_host;

        // output valid css for the top/right/bottom/left margins
        // we're checking for the 'auto' string and if it's not present
        // we just add 'px' to the end of the string

        $kinkySliderConfig=array();

        foreach ($this->kinkyslider_config as $oneRowOfConfig) {

            $kinkySliderConfig[$oneRowOfConfig['kinky_key']]=$oneRowOfConfig['kinky_value'];

        }

        $_testSliderMarginTop=$kinkySliderConfig['marginTop'];
        $_testSliderMarginRight=$kinkySliderConfig['marginRight'];
        $_testSliderMarginBottom=$kinkySliderConfig['marginBottom'];
        $_testSliderMarginLeft=$kinkySliderConfig['marginLeft'];

        $_testSliderMarginTop=($_testSliderMarginTop!='auto')?$_testSliderMarginTop.'px':$_testSliderMarginTop;
        $_testSliderMarginRight=($_testSliderMarginRight!='auto')?$_testSliderMarginRight.'px':$_testSliderMarginRight;
        $_testSliderMarginBottom=($_testSliderMarginBottom!='auto')?$_testSliderMarginBottom.'px':$_testSliderMarginBottom;
        $_testSliderMarginLeft=($_testSliderMarginLeft!='auto')?$_testSliderMarginLeft.'px':$_testSliderMarginLeft;

        $kinkySliderConfig['marginTop']=$_testSliderMarginTop;
        $kinkySliderConfig['marginRight']=$_testSliderMarginRight;
        $kinkySliderConfig['marginBottom']=$_testSliderMarginBottom;
        $kinkySliderConfig['marginLeft']=$_testSliderMarginLeft;


        $kinkySliderData=array();

        foreach ($this->kinkyslider_output_images as $kinkySliderOutputSlide) {

            if ($kinkySliderOutputSlide['kinky_active']==1) {

                $kinkySliderData[]=array(
                    'image'=>_MODULE_DIR_.$this->name.'/uploads/images/'.$kinkySliderOutputSlide['kinky_imagedir'].'/'.KINKYSLIDER_DEFAULT_FILE_NAME.'.png',
                    'link'=>$kinkySliderOutputSlide['kinky_link'],
                    'header'=>$kinkySliderOutputSlide['kinky_header'],
                    'price'=>$kinkySliderOutputSlide['kinky_price'],

                );
            }
        }

        $kinkySliderDataReverse = array_reverse($kinkySliderData);

        $smarty->assign('kinkyslider_data', $kinkySliderDataReverse);
        $smarty->assign('kinkyslider_config', $kinkySliderConfig);

        $smarty->assign('kinkyslider_calculated_image_width',$this->kinkyslider_calculated_image_width);
        $smarty->assign('kinkyslider_calculated_image_height',$this->kinkyslider_calculated_image_height);

        return $this->display(__FILE__, 'kinkyslider.tpl');

    }

    function hookLeftColumn($params) {

        return $this->hookFooter($params);

    }

    function hookRightColumn($params) {

        return $this->hookFooter($params);

    }

    function hookTop($params) {

        return $this->hookFooter($params);

    }

    public function hookHeader($params) {

        return $this->hookFooter($params);

    }



    private function _multidimensionalSearch($parents, $searched) {

      if (empty($searched) || empty($parents)) {
        return false;
      }

      foreach ($parents as $key => $value) {
        $exists = true;
        foreach ($searched as $skey => $svalue) {
          $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
        }
        if($exists){ return $key; }
      }

      return false;
    }

    function bazinga_last_dir($subdir) {

        $files = $this->directoryToArray($_SERVER['DOCUMENT_ROOT']._MODULE_DIR_.$this->name.'/uploads/'.$subdir.'/', false);
        sort($files);
        $lista=end($files);
        if (($lista=='') || ($lista==null)) { $lista='0'; }
        return ($lista);
    }

    function directoryToArray($directory, $recursive) {

            $array_items = array();
            if ($handle = opendir($directory)) {
                    while (false !== ($file = readdir($handle))) {
                            if ($file != "." && $file != "..") {

                                    if (is_dir($directory. "/" . $file)) {

                                            $array_items[] = preg_replace("/\/\//si", "/", $file);
                                    }


                            }
                    }

                    closedir($handle);
            }

            return $array_items;
    }

    function bazinga_load($filename) {

            $image_info = getimagesize($filename);
            $this->kinkyslider_image_type = $image_info[2];
            if( $this->kinkyslider_image_type == IMAGETYPE_JPEG ) {
                    $this->kinkyslider_image = imagecreatefromjpeg($filename);
            } elseif( $this->kinkyslider_image_type == IMAGETYPE_GIF ) {
                    $this->kinkyslider_image = imagecreatefromgif($filename);
            } elseif( $this->kinkyslider_image_type == IMAGETYPE_PNG ) {
                    $this->kinkyslider_image = imagecreatefrompng($filename);
            }
    }

    function bazinga_save($filename, $image_type=IMAGETYPE_PNG, $compression=90, $permissions=null) {

            if( $image_type == IMAGETYPE_JPEG ) {
                    imagejpeg($this->kinkyslider_image,$filename,$compression);
            } elseif( $image_type == IMAGETYPE_GIF ) {
                    imagegif($this->kinkyslider_image,$filename);
            } elseif( $image_type == IMAGETYPE_PNG ) {
                    imagepng($this->kinkyslider_image,$filename);
            }

            if( $permissions != null) {
               chmod($filename,$permissions);
            }
    }


   function bazinga_getWidth() {

           return imagesx($this->kinkyslider_image);
   }

   function bazinga_getHeight() {

           return imagesy($this->kinkyslider_image);
   }

   function bazinga_resize($width,$height) {

           $new_image = imagecreatetruecolor($width, $height);
           imagecopyresampled($new_image, $this->kinkyslider_image, 0, 0, 0, 0, $width, $height, $this->bazinga_getWidth(), $this->bazinga_getHeight());
           $this->kinkyslider_image = $new_image;

   }

   function bazinga_resizeZoomCrop($new_width,$new_height) {

           $new_image = imagecreatetruecolor($new_width, $new_height);

           $width=$this->bazinga_getWidth();
           $height=$this->bazinga_getHeight();

           $src_x = $src_y = 0;
           $src_w = $width;
           $src_h = $height;

           $cmp_x = $width / $new_width;
           $cmp_y = $height / $new_height;


           if ($cmp_x > $cmp_y) {

                $src_w = round (($width / $cmp_x * $cmp_y));
                $src_x = round (($width - ($width / $cmp_x * $cmp_y)) / 2);

           } else if ($cmp_y > $cmp_x) {

                $src_h = round (($height / $cmp_y * $cmp_x));
                $src_y = round (($height - ($height / $cmp_y * $cmp_x)) / 2);

           }

           imagecopyresampled ($new_image,  $this->kinkyslider_image, 0, 0, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);
           $this->kinkyslider_image=$new_image;
   }

}

// End of: kinkyslider.php

?>
