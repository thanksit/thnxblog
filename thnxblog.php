<?php
include_once _PS_MODULE_DIR_.'thnxblog/config/define.inc.php';
include_once _PS_MODULE_DIR_.'thnxblog/classes/thnximagetypeclass.php';
include_once _PS_MODULE_DIR_.'thnxblog/classes/thnxcategorypostclass.php';
include_once _PS_MODULE_DIR_.'thnxblog/classes/thnxcommentclass.php';
include_once _PS_MODULE_DIR_.'thnxblog/classes/thnxcategoryclass.php';
include_once _PS_MODULE_DIR_.'thnxblog/classes/thnxpostsclass.php';
include_once _PS_MODULE_DIR_.'thnxblog/classes/thnxpostmetaclass.php';
include_once _PS_MODULE_DIR_.'thnxblog/controllers/front/main.php';
class thnxblog extends Module
{
	public static $thnxblogshortname = 'thnxblog';
	public static $quick_key = 'thnxblogquickaceslink';
	public static $thnxlinkobj;
	public static $dispatcherobj;
	public static $inlinejs = array();
	public $all_hooks = array("displayheader","ModuleRoutes");
	public $fields_arr_path = '/data/fields_array.php';
	public $css_files = array(
		array(
			'key' => 'thnxblog_css',
			'src' => 'thnxblog.css',
			'priority' => 250,
			'media' => 'all',
			'load_theme' => false,
		),
	);
	public $js_files = array(
		array(
			'key' => 'thnxblog_js',
			'src' => 'thnxblog.js',
			'priority' => 250,
			'position' => 'bottom', // bottom or head
			'load_theme' => false,
		),
		array(
			'key' => 'thnxblog_validator_js',
			'src' => 'validator.min.js',
			'priority' => 250,
			'position' => 'bottom', // bottom or head
			'load_theme' => false,
		),
	);
	public $all_tabs = array(
		array(
	        'class_name' => 'Adminthnxpost',
	        'id_parent' => 'parent',
	        'name' => 'Blog Posts',
		),
		array(
	        'class_name' => 'Adminthnxcategory',
	        'id_parent' => 'parent',
	        'name' => 'Blog Categories',
		),
		array(
	        'class_name' => 'Adminthnxcomment',
	        'id_parent' => 'parent',
	        'name' => 'Blog Comments',
		),
		array(
	        'class_name' => 'Adminthnximagetype',
	        'id_parent' => 'parent',
	        'name' => 'Blog Image Type',
		),
	);
	public $dbfiles = '/db/dbfiles.php';
	public static $ModuleName = 'thnxblog';
	public function __construct()
	{
		$this->name = 'thnxblog';
		$this->tab = 'front_office_features';
		$this->version = '2.0.0';
		$this->author = 'thanksit.com';
		$this->bootstrap = true;
        $this->need_upgrade = true;
		$this->controllers = array('archive','single');
		parent::__construct();	
		$this->displayName = $this->l('Platinum Theme Powerfull Prestashop Blog Module by thanksit.com');
		$this->description = $this->l('thnxBlog Powerfull Prestashop Blog Module by thanksit.com');
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        if(!isset($this->context)){
			$this->context = Context::getContext();
        }
	}
	public function install()
	{
		if(!parent::install()
		 || !$this->Register_Hooks()
		 || !$this->Register_Tabs()
		 || !$this->Register_SQL()
		 || !$this->AddQuickAccessLink()
		 || !$this->DummyData()
		 || !$this->InstallSampleData()
		)
			return false;
		return true;
	}
	public function uninstall()
	{
		if(!parent::uninstall()
		 || !$this->UnRegister_Hooks()
		 || !$this->UnRegister_Tabs()
		 || !$this->UnRegister_SQL()
		 || !$this->UnInstallSampleData()
		 || !$this->DeleteQuickAccessLink()
		)
			return false;
		return true;
	}
	public function AddQuickAccessLink(){
	    $link = new Link();
	    $QuickAccess = new QuickAccess();
	    $QuickAccess->link = $link->getAdminLink('AdminModules').'&configure='.$this->name;
	    $languages = Language::getLanguages(false);
	    if(isset($languages) && !empty($languages))
	        foreach($languages as $language)
	            $QuickAccess->name[$language['id_lang']] = $this->l("thnxBlog Settings");
	    $QuickAccess->new_window = '0';
	    if($QuickAccess->save())
	        Configuration::updateValue(self::$quick_key,$QuickAccess->id);
	    return true;
	}
	public function DeleteQuickAccessLink(){
        $quick_key = (int)Configuration::get(self::$quick_key);
        if($quick_key != 0){
	        $QuickAccess = new QuickAccess($quick_key);
	        if($QuickAccess->delete()){
	        	return true;	
	        }
        }else{
        	return false;
        }
    }
	public static function thnxblog_js($params, $content, &$smarty)
	{
		if(isset($params['name']) && !empty($params['name']) && !empty($content)){
			self::$inlinejs[$params['name']] = $content;
		}
	}
	public function Register_Hooks()
	{	
        // $this->registerHook("displayAdminAfterHeader");
        $this->registerHook("displayBeforeBodyClosingTag");
		if(isset($this->all_hooks) && !empty($this->all_hooks)){
			foreach ($this->all_hooks as $hook) {
        		$this->registerHook($hook);
			}
		}
        return true;
	}
	public function hookdisplayBeforeBodyClosingTag($params)
	{
		if(isset(self::$inlinejs) && !empty(self::$inlinejs)){
			foreach (self::$inlinejs as $keyinlinejs => $valueinlinejs) {
				print $valueinlinejs;
			}
		}
	}
	public function UnRegister_Hooks()
	{
        // $hook_idm = Hook::getIdByName("displayAdminAfterHeader");
    	// $this->unregisterHook((int)$hook_idm);
		if(isset($this->all_hooks)){
			foreach ($this->all_hooks as $hook) {
        		$hook_id = Hook::getIdByName($hook);
    		    if(isset($hook_id) && !empty($hook_id)){
    		    	$this->unregisterHook((int)$hook_id);
    		    }
			}
		}
        return true;
	}
	public function Register_SQL()
	{
		$querys = array();
		if(file_exists(dirname(__FILE__).$this->dbfiles)){
			require_once(dirname(__FILE__).$this->dbfiles);
			if(isset($querys) && !empty($querys))
				foreach($querys as $query){
					if(!Db::getInstance()->Execute($query))
					    return false;
				}
		}
        return true;
	}
	public function UnRegister_SQL()
	{
		$querys_u = array();
		if(file_exists(dirname(__FILE__).$this->dbfiles)){
			require_once(dirname(__FILE__).$this->dbfiles);
			if(isset($querys_u) && !empty($querys_u))
				foreach($querys_u as $query_u){
					if(!Db::getInstance()->Execute($query_u))
					    return false;
				}
		}
        return true;
	}
	public function UnRegister_Tabs()
	{
		if(isset($this->all_tabs) && !empty($this->all_tabs)){
			foreach($this->all_tabs as $tab_list){
				$tab_list_id = Tab::getIdFromClassName($tab_list['class_name']);
			    if(isset($tab_list_id) && !empty($tab_list_id)){
			        $tabobj = new Tab($tab_list_id);
			        $tabobj->delete();
			    }
			}
		}
		$tabp_list_id = Tab::getIdFromClassName('Adminthnxblogdashboard');
		$tabpobj = new Tab($tabp_list_id);
	    $tabpobj->delete();
        return true;
	}
	public function hookModuleRoutes($params)
    {
    	$mainslug = Configuration::get(self::$thnxblogshortname."main_blog_url");
    	$postfixslug = Configuration::get(self::$thnxblogshortname."postfix_url_format");
    	$categoryslug = Configuration::get(self::$thnxblogshortname."category_blog_url");
    	$tagslug = Configuration::get(self::$thnxblogshortname."tag_blog_url");
    	$singleslug = Configuration::get(self::$thnxblogshortname."single_blog_url");
    	$main_slug = (isset($mainslug) && !empty($mainslug)) ? $mainslug : "thnxblog";
    	$postfix_slug = (isset($postfixslug) && !empty($postfixslug) && ($postfixslug == "enable_html")) ? ".html" : "";
    	$category_slug = (isset($categoryslug) && !empty($categoryslug)) ? $categoryslug : "category";
    	$tag_slug = (isset($tagslug) && !empty($tagslug)) ? $tagslug : "tag";
    	$single_slug = (isset($singleslug) && !empty($singleslug)) ? $singleslug : "post";
    	$params = array(
                        'fc' => 'module',
                        'module' => 'thnxblog'
                );
        $thnxblogroutes = array(
	        	'thnxblog-thnxblog-module' => array(
	        	    'controller' =>  'archive',
	        	    'rule' => $main_slug.$postfix_slug,
	        	    'keywords' => array(),
	        	    'params' => $params
	        	),
                'thnxblog-archive-module' => array(
                    'controller' =>  'archive',
                    'rule' =>        $main_slug.'/'.$category_slug.'/{id}_{rewrite}'.$postfix_slug,
                    'keywords' => array(
                        'id'   =>   array('regexp' => '[0-9]+', 'param' => 'id'),
                        'rewrite'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
                'thnxblog-archive-aftrid-module' => array(
                    'controller' =>  'archive',
                    'rule' =>        $main_slug.'/'.$category_slug.'/{rewrite}_{id}'.$postfix_slug,
                    'keywords' => array(
                        'id'   =>   array('regexp' => '[0-9]+', 'param' => 'id'),
                        'rewrite'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
                'thnxblog-archive-wid-module' => array(
                    'controller' =>  'archive',
                    'rule' =>        $main_slug.'/'.$category_slug.'/{rewrite}'.$postfix_slug,
                    'keywords' => array(
                        'id'   =>   array('regexp' => '[0-9]+', 'param' => 'id'),
                        'rewrite'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
                'thnxblog-tag-module' => array(
                    'controller' =>  'archive',
                    'rule' =>        $main_slug.'/'.$tag_slug.'/{id}_{rewrite}'.$postfix_slug,
                    'keywords' => array(
                        'id'   =>   array('regexp' => '[0-9]+', 'param' => 'id'),
                        'rewrite'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
                'thnxblog-tag-aftrid-module' => array(
                    'controller' =>  'archive',
                    'rule' =>        $main_slug.'/'.$tag_slug.'/{rewrite}_{id}'.$postfix_slug,
                    'keywords' => array(
                        'id'   =>   array('regexp' => '[0-9]+', 'param' => 'id'),
                        'rewrite'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
                'thnxblog-tag-wid-module' => array(
                    'controller' =>  'archive',
                    'rule' =>        $main_slug.'/'.$tag_slug.'/{rewrite}'.$postfix_slug,
                    'keywords' => array(
                        'id'   =>   array('regexp' => '[0-9]+', 'param' => 'id'),
                        'rewrite'       =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
                'thnxblog-single-module' => array(
                    'controller' =>  'single',
                    'rule' =>        $main_slug.'/'.$single_slug.'/{id}_{rewrite}'.$postfix_slug,
                    'keywords' => array(
                        'id' =>   array('regexp' => '[0-9]+','param' => 'id'),
                        'rewrite' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
                'thnxblog-single-aftrid-module' => array(
                    'controller' =>  'single',
                    'rule' =>        $main_slug.'/'.$single_slug.'/{rewrite}_{id}'.$postfix_slug,
                    'keywords' => array(
                        'id' =>   array('regexp' => '[0-9]+','param' => 'id'),
                        'rewrite' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
                'thnxblog-single-wid-module' => array(
                    'controller' =>  'single',
                    'rule' =>        $main_slug.'/'.$single_slug.'/{rewrite}'.$postfix_slug,
                    'keywords' => array(
                        'id' =>   array('regexp' => '[0-9]+','param' => 'id'),
                        'rewrite' =>   array('regexp' => '[_a-zA-Z0-9-\pL]*','param' => 'rewrite'),
                    ),
                    'params' => $params
                ),
            );
		return $thnxblogroutes;
    }
    public static function GetLinkObject(){
    	if(!isset(self::$thnxlinkobj) || empty(self::$thnxlinkobj)){
    		$ssl = false;
    		if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
    		    $ssl = true;
    		}
    		$protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
    		$useSSL = ((isset($ssl) && $ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
    		$protocol_content = ($useSSL) ? 'https://' : 'http://';
    		self::$thnxlinkobj = new Link($protocol_link, $protocol_content);
    	}
    	return self::$thnxlinkobj;
    }
    public static function getBaseLink($id_shop = null, $ssl = null, $relative_protocol = false)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        if ($relative_protocol) {
            $base = '//'.($ssl ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }

        return $base.$shop->getBaseURI();
    }
    public static function getLangLink($id_lang = null, Context $context = null, $id_shop = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$id_shop) {
            $id_shop = $context->shop->id;
        }
        $allow = (int)Configuration::get('PS_REWRITING_SETTINGS');
        if ((!$allow && in_array($id_shop, array($context->shop->id,  null))) || !Language::isMultiLanguageActivated($id_shop) || !(int)Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop)) {
            return '';
        }
        if (!$id_lang) {
            $id_lang = $context->language->id;
        }
        return Language::getIsoById($id_lang).'/';
    }
	public static function thnxBlogMainLink() {
		$id_shop = (int)Context::getcontext()->shop->id;
		$id_lang = (int)Context::getcontext()->language->id;
		$ssl = null;
		$relative_protocol = false;
		$url = self::getBaseLink($id_shop, $ssl, $relative_protocol).self::getLangLink($id_lang, null, $id_shop);
		return $url; 
	}
	public static function thnxBlogLink($rule = 'thnxblog-thnxblog-module',$params = array()){
		$context = Context::getContext();
		$id_lang = (int)$context->language->id;
		$id_shop = (int)$context->shop->id;
		$mainurl = self::thnxBlogMainLink();
		if(!isset(self::$dispatcherobj) || empty(self::$dispatcherobj)){
			self::$dispatcherobj = Dispatcher::getInstance();
		}
		$force_routes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
        return $mainurl.self::$dispatcherobj->createUrl($rule,$id_lang,$params,$force_routes);
    }
    public static function thnxBlogPostLink($params = array()){
    	$url_format = Configuration::get(self::$thnxblogshortname."url_format");
    	if(isset($params['id']) && !isset($params['rewrite'])){
    		$params['rewrite'] = thnxpostsclass::get_the_rewrite($params['id']);
    	}
    	if(!isset($params['id']) && isset($params['rewrite'])){
    		$params['id'] = thnxpostsclass::get_the_id($params['rewrite']);
    	}
    	if($url_format == 'preid_seo_url'){
    		$rule = 'thnxblog-single-module';
    		return self::thnxBlogLink($rule,$params);
    	}elseif ($url_format == 'postid_seo_url') {
    		$rule = 'thnxblog-single-aftrid-module';
    		return self::thnxBlogLink($rule,$params);
    	}elseif ($url_format == 'wthotid_seo_url') {
    		$rule = 'thnxblog-single-wid-module';
    		return self::thnxBlogLink($rule,$params);
    	}elseif ($url_format == 'default_seo_url') {
    		return self::GetLinkObject()->getModuleLink("thnxblog","single",$params);
    	}else{
    		$rule = 'thnxblog-single-module';
    	}
    }
    public static function thnxBlogTagLink($params = array()){
    	$url_format = Configuration::get(self::$thnxblogshortname."url_format");
    	if($url_format == 'preid_seo_url'){
    		$rule = 'thnxblog-tag-module';
    		return self::thnxBlogLink($rule,$params);
    	}elseif ($url_format == 'postid_seo_url') {
    		$rule = 'thnxblog-tag-aftrid-module';
    		return self::thnxBlogLink($rule,$params);
    	}elseif ($url_format == 'wthotid_seo_url') {
    		$rule = 'thnxblog-tag-wid-module';
    		return self::thnxBlogLink($rule,$params);
    	}elseif ($url_format == 'default_seo_url') {
    		return self::GetLinkObject()->getModuleLink("thnxblog","archive",$params);
    	}else{
    		$rule = 'thnxblog-tag-module';
    		return self::thnxBlogLink($rule,$params);
    	}
    }
    public static function thnxBlogCategoryLink($params = array()){
        $url_format = Configuration::get(self::$thnxblogshortname."url_format");
        if($url_format == 'preid_seo_url'){
        	$rule = 'thnxblog-archive-module';
        	return self::thnxBlogLink($rule,$params);
        }elseif ($url_format == 'postid_seo_url') {
        	$rule = 'thnxblog-archive-aftrid-module';
        	return self::thnxBlogLink($rule,$params);
        }elseif ($url_format == 'wthotid_seo_url') {
        	$rule = 'thnxblog-archive-wid-module';
        	return self::thnxBlogLink($rule,$params);
        }elseif ($url_format == 'default_seo_url') {
        	return self::GetLinkObject()->getModuleLink("thnxblog","archive",$params);
        }else{
        	$rule = 'thnxblog-archive-module';
        	return self::thnxBlogLink($rule,$params);
        }
    }
    /* thnxblog::GetThemeName()  */
	public static function GetThemeName(){
		$theme_name = Configuration::get(self::$thnxblogshortname."theme_name");
		if(isset($theme_name) && !empty($theme_name)){
			return $theme_name;
		}else{
			return "default";
		}
	}
	public function Register_ETabs(){
		$tabpar_listobj = new Tab();
		$langs = Language::getLanguages();
		$id_parent = (int)Tab::getIdFromClassName("IMPROVE");
		$tabpar_listobj->class_name = 'Adminthnxblogdashboard';
		$tabpar_listobj->id_parent = $id_parent;
		$tabpar_listobj->module = $this->name;
		foreach($langs as $l)
	    {
	    	$tabpar_listobj->name[$l['id_lang']] = $this->l("ThnxBlog");
	    }
	    if($tabpar_listobj->save()){
	    	return (int)$tabpar_listobj->id;
	    }else{
	    	return (int)$id_parent;
	    }
	}
	public function Register_Tabs()
	{
		$tabs_lists = array();
        $langs = Language::getLanguages();
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $save_tab_id = $this->Register_ETabs();
    	if(isset($this->all_tabs) && !empty($this->all_tabs)){
    		foreach ($this->all_tabs as $tab_list)
    		{
    		    $tab_listobj = new Tab();
    		    $tab_listobj->class_name = $tab_list['class_name'];
    		    $tab_listobj->id_parent = $save_tab_id;
    		    if(isset($tab_list['module']) && !empty($tab_list['module'])){
    		    	$tab_listobj->module = $tab_list['module'];
    		    }else{
    		    	$tab_listobj->module = $this->name;
    		    }
    		    foreach($langs as $l)
    		    {
    		    	$tab_listobj->name[$l['id_lang']] = $this->l($tab_list['name']);
    		    }
    		    $tab_listobj->save();
    		}
    	}
        return true;
    }
    // Start Setting
    public function InstallSampleData()
    {
        $multiple_arr = array();
        $this->AllFields();
        foreach($this->fields_form as $key => $value){
        	if(empty($multiple_arr)){
        		$multiple_arr = $value['form']['input'];
        	}else{
            	$multiple_arr = array_merge($multiple_arr,$value['form']['input']);
        	}
        }
        // START LANG
		$languages = Language::getLanguages(false);
        if(isset($multiple_arr) && !empty($multiple_arr)){
            foreach($multiple_arr as $mvalue){
                if(isset($mvalue['lang']) && $mvalue['lang'] == true && isset($mvalue['name'])){
                   foreach($languages as $lang){
	                   	if(isset($mvalue['default_val'])){
	                    	${$mvalue['name'].'_lang'}[$lang['id_lang']] = $mvalue['default_val'];
	                   	}
                   }
                }
            }
        }
        // END LANG
        if(isset($multiple_arr) && !empty($multiple_arr)){
            foreach($multiple_arr as $mvalue){
                if(isset($mvalue['lang']) && $mvalue['lang'] == true && isset($mvalue['name'])){
                    Configuration::updateValue(self::$thnxblogshortname.$mvalue['name'],${$mvalue['name'].'_lang'});
                }else{
                    if(isset($mvalue['name'])){
                    	if(isset($mvalue['default_val'])){
                        	Configuration::updateValue(self::$thnxblogshortname.$mvalue['name'],$mvalue['default_val']);
                    	}
                    }
                }
            }
        }
        return true;
    }
    public function UnInstallSampleData()
    {
        $multiple_arr = array();
        $this->AllFields();
        foreach($this->fields_form as $key => $value){
            if(empty($multiple_arr)){
        		$multiple_arr = $value['form']['input'];
        	}else{
            	$multiple_arr = array_merge($multiple_arr,$value['form']['input']);
        	}
        }
        if(isset($multiple_arr) && !empty($multiple_arr)){
            foreach($multiple_arr as $mvalue){
                if(isset($mvalue['name'])){
                    Configuration::deleteByName(self::$thnxblogshortname.$mvalue['name']);
                }
            }
        }
        return true;
    }
    public function AllFields()
    {
    	$thnxblog_settings = array();
        include_once(dirname(__FILE__).$this->fields_arr_path);
        if($this->getConfigPath()){
        	include_once($this->getConfigPath());
        }
        if(isset($thnxblog_settings) && !empty($thnxblog_settings)){
        	foreach ($thnxblog_settings as $thnxblog_setting) {
        		$this->fields_form[]['form'] = $thnxblog_setting;
        	}
        }
        return $this->fields_form;
    }
    public function AsignGlobalSettingValue(){
    	$thnxblogsettings = $this->GetSettingsValueS();
    	$this->smarty->assignGlobal('thnxblogsettings',$thnxblogsettings);
    	return true;
    }
    public static function GetAllThemes(){
    	$results = array();
    	$theme_dirs = _PS_THEME_DIR_.'modules/'.thnxblog_tpl_dir;
    	$module_dirs = _PS_MODULE_DIR_.thnxblog_tpl_dir;

    	if(is_dir($theme_dirs)){
    		$scandir = scandir($theme_dirs);
    		$all_folders = array_diff($scandir, array('..', '.'));
    	}elseif(is_dir($module_dirs)){
    		$scandir = scandir($module_dirs);
    		$all_folders = array_diff($scandir, array('..', '.'));
    	}
    	if(isset($all_folders) && !empty($all_folders)){
    		$i = 0;
    		foreach ($all_folders as $folder) {
    			$results[$i]['id'] = $folder;
    			$results[$i]['name'] = ucwords($folder);
    			$i++;
    		}
    	}
    	return $results;
    }
    public function GetSettingsValueS()
    {
        $id_lang = Context::getcontext()->language->id;
        $multiple_arr = array();
        $thnxblogsettings = array();
        $this->AllFields();
        foreach($this->fields_form as $key => $value){
            $multiple_arr = array_merge($multiple_arr,$value['form']['input']);
        }
        if(isset($multiple_arr) && !empty($multiple_arr)){
            foreach($multiple_arr as $mvalue){
                if(isset($mvalue['lang']) && $mvalue['lang'] == true && isset($mvalue['name'])){
                    $thnxblogsettings[$mvalue['name']] = Configuration::get(self::$thnxblogshortname.$mvalue['name'],$id_lang);
                }else{
                    if(isset($mvalue['name'])){
                        $thnxblogsettings[$mvalue['name']] = Configuration::get(self::$thnxblogshortname.$mvalue['name']);
                    }
                }
            }
        }
        return $thnxblogsettings;
    }
    public static function isEmptyFileContet($path = null){
    	if($path == null)
    		return false;
    	if(file_exists($path)){
    		$content = Tools::file_get_contents($path);
    		if(empty($content)){
    			return false;
    		}else{
    			return true;
    		}
    	}else{
    		return false;
    	}
    }
    public function Register_Css()
    {
        if(isset($this->css_files) && !empty($this->css_files)){
        	$theme_name = $this->context->shop->theme_name;
    		$page_name = $this->context->controller->php_self;
    		$root_path = _PS_ROOT_DIR_.'/';
        	foreach($this->css_files as $css_file):
        		if(isset($css_file['key']) && !empty($css_file['key']) && isset($css_file['src']) && !empty($css_file['src'])){
        			$media = (isset($css_file['media']) && !empty($css_file['media'])) ? $css_file['media'] : 'all';
        			$priority = (isset($css_file['priority']) && !empty($css_file['priority'])) ? $css_file['priority'] : 50;
        			$page = (isset($css_file['page']) && !empty($css_file['page'])) ? $css_file['page'] : array('all');
        			if(is_array($page)){
        				$pages = $page;
        			}else{
        				$pages = array($page);
        			}
        			if(in_array($page_name, $pages) || in_array('all', $pages)){
        				if(isset($css_file['load_theme']) && ($css_file['load_theme'] == true)){
        					$theme_file_src = 'themes/'.$theme_name.'/assets/css/'.$css_file['src'];
        					if(self::isEmptyFileContet($root_path.$theme_file_src)){
        						$this->context->controller->registerStylesheet($css_file['key'], $theme_file_src , ['media' => $media, 'priority' => $priority]);
        					}
        				}else{
        					$module_file_src = 'modules/'.$this->name.'/css/'.$css_file['src'];
        					if(self::isEmptyFileContet($root_path.$module_file_src)){
        						$this->context->controller->registerStylesheet($css_file['key'], $module_file_src , ['media' => $media, 'priority' => $priority]);
        					}
        				}
    				}
        		}
        	endforeach;
        }
        return true;
    }
    public function Register_Js()
    {
        if(isset($this->js_files) && !empty($this->js_files)){
	    	$theme_name = $this->context->shop->theme_name;
			$page_name = $this->context->controller->php_self;
			$root_path = _PS_ROOT_DIR_.'/';
        	foreach($this->js_files as $js_file):
        		if(isset($js_file['key']) && !empty($js_file['key']) && isset($js_file['src']) && !empty($js_file['src'])){
        			$position = (isset($js_file['position']) && !empty($js_file['position'])) ? $js_file['position'] : 'bottom';
        			$priority = (isset($js_file['priority']) && !empty($js_file['priority'])) ? $js_file['priority'] : 50;
        			$page = (isset($css_file['page']) && !empty($css_file['page'])) ? $css_file['page'] : array('all');
        			if(is_array($page)){
        				$pages = $page;
        			}else{
        				$pages = array($page);
        			}
        			if(in_array($page_name, $pages) || in_array('all', $pages)){
	        			if(isset($js_file['load_theme']) && ($js_file['load_theme'] == true)){
	        				$theme_file_src = 'themes/'.$theme_name.'/assets/js/'.$js_file['src'];
	        				if(self::isEmptyFileContet($root_path.$theme_file_src)){
	        					$this->context->controller->registerJavascript($js_file['key'], $theme_file_src , ['position' => $position, 'priority' => $priority]);
	        				}
	        			}else{
		        			$module_file_src = 'modules/'.$this->name.'/js/'.$js_file['src'];
	        				if(self::isEmptyFileContet($root_path.$module_file_src)){
		        				$this->context->controller->registerJavascript($js_file['key'], $module_file_src , ['position' => $position, 'priority' => $priority]);
	        				}
	        			}
        			}
        		}
        	endforeach;
        }
        return true;
    }
    public function hookdisplayheader()
    {
    	$base_url = $this->context->shop->getBaseURL(true, true);
    	Media::addJsDef(array('thnx_base_dir' => $base_url));
        if((isset($this->context->controller->controller_type)) && ($this->context->controller->controller_type == 'front' || $this->context->controller->controller_type == 'modulefront')){
			global $smarty;
			smartyRegisterFunction($smarty, 'block', 'thnxblog_js', array('thnxblog', 'thnxblog_js'));
		}
    	$this->Register_Css();
    	$this->Register_Js();
    }
    public function GenerateImageThumbnail($select_image_type = 'all'){
    	$dir = _PS_MODULE_DIR_.self::$ModuleName.'/img/';
    	$GetAllImageTypes = thnximagetypeclass::GetAllImageTypes();
    	if($select_image_type == 'all' || $select_image_type == 'category'){
			// start category
			$categories = thnxcategoryclass::GetCategories();
			if(isset($categories) && !empty($categories)){
				foreach ($categories as $category) {
					if(isset($category['category_img']) && !empty($category['category_img']) && file_exists($dir.$category['category_img'])){
						$ext = substr($category['category_img'], strrpos($category['category_img'], '.') + 1);
					    	if(isset($GetAllImageTypes) && !empty($GetAllImageTypes)){
						        foreach($GetAllImageTypes as $imagetype){
						        	ImageManager::resize($dir.$category['category_img'],$dir.$imagetype['name'].'-'.$category['category_img'],(int)$imagetype['width'],(int)$imagetype['height'],$ext);
						        }
							}
					}
				}
			}
			// End category
		}
		if($select_image_type == 'all' || $select_image_type == 'gallery' || $select_image_type == 'post'){
			$posts_count = thnxpostsclass::GetCategoryPostsCount();
			$all_posts = thnxpostsclass::GetCategoryPosts(NULL,1,$posts_count,'post','DESC');
		}
    	if($select_image_type == 'all' || $select_image_type == 'post'){
			// Start Post Image
			if(isset($all_posts) && !empty($all_posts)){
				foreach($all_posts as $all_post){
					if(isset($all_post['post_img']) && !empty($all_post['post_img']) && file_exists($dir.$all_post['post_img'])){
						$ext = substr($all_post['post_img'], strrpos($all_post['post_img'], '.') + 1);
					    	if(isset($GetAllImageTypes) && !empty($GetAllImageTypes)){
						        foreach($GetAllImageTypes as $imagetype){
						        	ImageManager::resize($dir.$all_post['post_img'],$dir.$imagetype['name'].'-'.$all_post['post_img'],(int)$imagetype['width'],(int)$imagetype['height'],$ext);
						        }
							}
					}
				}
			}
			// End Post Image
		}
    	if($select_image_type == 'all' || $select_image_type == 'gallery'){
			// Start gallery Image
			if(isset($all_posts) && !empty($all_posts)){
				foreach($all_posts as $all_post){
					if(isset($all_post['gallery']) && !empty($all_post['gallery'])){
						$gallery = @explode(",",$all_post['gallery']);
						if(isset($gallery) && !empty($gallery) && is_array($gallery)){
							foreach ($gallery as $gall) {
								if(file_exists($dir.$gall)){
									$ext = substr($gall, strrpos($gall, '.') + 1);
							    	if(isset($GetAllImageTypes) && !empty($GetAllImageTypes)){
								        foreach($GetAllImageTypes as $imagetype){
								        	ImageManager::resize($dir.$gall,$dir.$imagetype['name'].'-'.$gall,(int)$imagetype['width'],(int)$imagetype['height'],$ext);
								        }
									}
								}
							}
						}
					}
				}
			}
			// End gallery Image
		}
    }
    public function getContent()
    {
    	// 	    $id_lang = (int)Context::getContext()->language->id;
	    // $id_shop = (int)Context::getContext()->shop->id;
	    // include_once(dirname(__FILE__).'/data/dummy_data.php');
	    // $this->InsertDummyData($thnxblog_imagetype,'thnximagetypeclass');
	    
    	if(Tools::isSubmit('submit_generateimage')){
        	$select_image_type = Tools::getValue('select_image_type');
        	$this->GenerateImageThumbnail($select_image_type);
        }
    	$this->context->controller->addJqueryPlugin('tagify');
        Configuration::updateValue('thnxblogshortname',self::$thnxblogshortname);
        $html = '';
        $multiple_arr = array();
        // START RENDER FIELDS
        $this->AllFields();
        // END RENDER FIELDS
        if(Tools::isSubmit('save'.$this->name)){
            foreach($this->fields_form as $key => $value){
                $multiple_arr = array_merge($multiple_arr,$value['form']['input']);
            }
            // START LANG
            $languages = Language::getLanguages(false);
            if(isset($multiple_arr) && !empty($multiple_arr)){
                foreach($multiple_arr as $mvalue){
                    if(isset($mvalue['lang']) && $mvalue['lang'] == true && isset($mvalue['name'])){
                       foreach($languages as $lang){
                        ${$mvalue['name'].'_lang'}[$lang['id_lang']] = Tools::getvalue($mvalue['name'].'_'.$lang['id_lang']);
                       }
                    }
                }
            }
            // END LANG
            if(isset($multiple_arr) && !empty($multiple_arr)){
                foreach($multiple_arr as $mvalue){
                    if(isset($mvalue['lang']) && $mvalue['lang'] == true && isset($mvalue['name'])){
                            Configuration::updateValue(self::$thnxblogshortname.$mvalue['name'],${$mvalue['name'].'_lang'});
                    }else{
                        if(isset($mvalue['name'])){
                            Configuration::updateValue(self::$thnxblogshortname.$mvalue['name'],Tools::getvalue($mvalue['name']));
                        }
                    }
                }
            }
            $helper = $this->SettingForm();
            $html_form = $helper->generateForm($this->fields_form);
            $html .= $this->displayConfirmation($this->l('Successfully Saved All Fields Values.'));
            $html .= $html_form;
        }else{
            $helper = $this->SettingForm();
            $html_form = $helper->generateForm($this->fields_form);
            $html .= $html_form;
        }
        return $html;
    }
    public function SettingForm() {
    	$languages = Language::getLanguages(false);
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->AllFields();
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        foreach ($languages as $lang)
                $helper->languages[] = array(
                        'id_lang' => $lang['id_lang'],
                        'iso_code' => $lang['iso_code'],
                        'name' => $lang['name'],
                        'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
                );
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save'.$this->name.'token=' . Tools::getAdminTokenLite('AdminModules'),
            )
        );
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'save'.$this->name;
        $multiple_arr = array();

        foreach($this->fields_form as $key => $value) {
        	if(empty($multiple_arr)){
        		if(isset($value['form']['input']) && !empty($value['form']['input'])){
        			$multiple_arr = $value['form']['input'];
        		}
        	}else{
        		if(isset($value['form']['input']) && !empty($value['form']['input'])){
        			$multiple_arr = array_merge($multiple_arr,$value['form']['input']);
        		}
        	}
        }
        foreach($multiple_arr as $mvalue){
            if(isset($mvalue['lang']) && $mvalue['lang'] == true && isset($mvalue['name'])){
               foreach($languages as $lang){
                    $helper->fields_value[$mvalue['name']][$lang['id_lang']] = Configuration::get(self::$thnxblogshortname.$mvalue['name'],$lang['id_lang']);
               }
            }else{
                if(isset($mvalue['name'])){
                    $helper->fields_value[$mvalue['name']] = Configuration::get(self::$thnxblogshortname.$mvalue['name']);
                }
            }
        }
        return $helper;
    }
    public function getConfigPath()
    {
    	$template = 'settings.php';
    	$themename = self::GetThemeName();
        if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.thnxblog::$ModuleName.'/views/templates/front/'.$themename.'/'.$template)) {
            return _PS_THEME_DIR_.'modules/'.thnxblog::$ModuleName.'/views/templates/front/'.$themename.'/'.$template;
        } elseif (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.thnxblog::$ModuleName.'/views/templates/front/'.$template)) {
            return _PS_THEME_DIR_.'modules/'.thnxblog::$ModuleName.'/views/templates/front/'.$template;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.thnxblog::$ModuleName.'/views/templates/front/'.$themename.'/'.$template)) {
            return _PS_MODULE_DIR_.thnxblog::$ModuleName.'/views/templates/front/'.$themename.'/'.$template;
    	} elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.thnxblog::$ModuleName.'/views/templates/front/'.$template)) {
            return _PS_MODULE_DIR_.thnxblog::$ModuleName.'/views/templates/front/'.$template;
        }
        return false;
    }
    // end settings
    /*  thnxblog::UploadMedia('image'); */
    public static function UploadMedia($name,$dir=NULL)
    {
    	if($dir == NULL){
    		$dir = _PS_MODULE_DIR_.self::$ModuleName.'/img/';
    	}
		$file_name = false;
		if (isset($_FILES[$name]) && isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
			$ext = substr($_FILES[$name]['name'], strrpos($_FILES[$name]['name'], '.') + 1);
			$basename_file_name = basename($_FILES[$name]["name"]);
			$strlen = strlen($basename_file_name);
			$strlen_ext = strlen($ext);
			$basename_file_name = substr($basename_file_name,0,($strlen-$strlen_ext));
			$link_rewrite_file_name = Tools::link_rewrite($basename_file_name);
			$file_name = $link_rewrite_file_name.'.'.$ext;
			$path = $dir.$file_name;
			$GetAllImageTypes = thnximagetypeclass::GetAllImageTypes();
			if(!move_uploaded_file($_FILES[$name]['tmp_name'],$path)) {
				return false;
			}else{
				if(isset($GetAllImageTypes) && !empty($GetAllImageTypes)){
			        foreach($GetAllImageTypes as $imagetype){
			        	ImageManager::resize($path,$dir.$imagetype['name'].'-'.$file_name,(int)$imagetype['width'],(int)$imagetype['height'],$ext);
			        }
				}
				return $file_name;
			}
		}else{
			return $file_name;
		}
	}
    public static function BulkUploadMedia($name,$dir=NULL)
    {
    	if($dir == NULL){
    		$dir = _PS_MODULE_DIR_.self::$ModuleName.'/img/';
    	}
    	$results_imgs = array();
		if (isset($_FILES[$name]) && isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
			foreach ($_FILES[$name]['name'] as $fileskey => $filesvalue) {
				// start upload
			if (isset($_FILES[$name]) && isset($_FILES[$name]['tmp_name'][$fileskey]) && !empty($_FILES[$name]['tmp_name'][$fileskey])) {
					$ext = substr($_FILES[$name]['name'][$fileskey], strrpos($_FILES[$name]['name'][$fileskey], '.') + 1);
					$basename_file_name = basename($_FILES[$name]["name"][$fileskey]);
					$strlen = strlen($basename_file_name);
					$strlen_ext = strlen($ext);
					$basename_file_name = substr($basename_file_name,0,($strlen-$strlen_ext));
					$link_rewrite_file_name = Tools::link_rewrite($basename_file_name);
					$file_name = $link_rewrite_file_name.'.'.$ext;
					$path = $dir.$file_name;
					$GetAllImageTypes = thnximagetypeclass::GetAllImageTypes();
					if(move_uploaded_file($_FILES[$name]['tmp_name'][$fileskey],$path)) {
						if(isset($GetAllImageTypes) && !empty($GetAllImageTypes)){
					        foreach($GetAllImageTypes as $imagetype){
					        	ImageManager::resize($path,$dir.$imagetype['name'].'-'.$file_name,(int)$imagetype['width'],(int)$imagetype['height'],$ext);
					        }
						}
						$results_imgs[] = $file_name;
					}
				}
				// end upload
			}
			return $results_imgs;
		}else{
			return $results_imgs;
		}
	}
    public function hookexecute()
	{
		$results = array();
		$this->context->smarty->assign(array('results' => $results));
		return $this->display(__FILE__,'views/templates/front/thnxblog.tpl');
	}
	public function InsertDummyData($categories,$class){
		$languages = Language::getLanguages(false);
	    if(isset($categories) && !empty($categories)){
	        $classobj = new $class();
	        foreach($categories as $valu){
	        	if(isset($valu['lang']) && !empty($valu['lang'])){
	        		foreach ($valu['lang'] as $valukey => $value){
	        			foreach ($languages as $language){
	        				if(isset($valukey)){
	        					$classobj->{$valukey}[$language['id_lang']] = isset($value) ? $value : '';
	        				}
	        			}
	        		}
	        	}
        		if(isset($valu['notlang']) && !empty($valu['notlang'])){
        			foreach ($valu['notlang'] as $valukey => $value){
        				if(isset($valukey)){
        					if($valukey == "id_shop"){
        						$classobj->{$valukey} = (int)Context::getContext()->shop->id;
        					}else{
        						$classobj->{$valukey} = $value;
        					}
        				}
        			}
        		}
	        	$classobj->add();
	        }
	    }
	}
	public function DummyData()
	{
	    $id_lang = (int)Context::getContext()->language->id;
	    $id_shop = (int)Context::getContext()->shop->id;
	    include_once(dirname(__FILE__).'/data/dummy_data.php');
	    $this->InsertDummyData($thnxblog_imagetype,'thnximagetypeclass');
	    $this->InsertDummyData($thnxblog_categories,'thnxcategoryclass');
	    $this->InsertDummyData($thnxblog_posts,'thnxpostsclass');
	    return true;
	}
	// public function hookdisplayAdminAfterHeader(){
	// 	$controller = Tools::getValue("controller");
	// 	$configure = Tools::getValue("configure");
	// 	$controllers = array("Adminthnxpost","Adminthnxcategory","Adminthnxcomment","Adminthnximagetype"); 
	// 	if(in_array($controller, $controllers)){
	// 		$data = @Tools::file_get_contents('http://thanksit.com/promotion/promotion_top.php');
	// 		if(isset($data) && !empty($data)){
	// 			print $data;
	// 		}else{
	// 			$url = Context::getContext()->shop->getBaseURL().'modules/thnxblog/views/templates/admin/thnxpost/helpers/form/';
	// 			$data = @Tools::file_get_contents($url.'promotion_top.php?url='.$url);
	// 			print $data;
	// 		}
	// 	}elseif ($controller == "AdminModules" && $configure == "thnxblog") {
	// 		$data = @Tools::file_get_contents('http://thanksit.com/promotion/promotion_top.php');
	// 		if(isset($data) && !empty($data)){
	// 			print $data;
	// 		}else{
	// 			$url = Context::getContext()->shop->getBaseURL().'modules/thnxblog/views/templates/admin/thnxpost/helpers/form/';
	// 			$data = @Tools::file_get_contents($url.'promotion_top.php?url='.$url);
	// 			print $data;
	// 		}
	// 	}else{
	// 		return false;
	// 	}
	// }
}