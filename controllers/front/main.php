<?php
class thnxblogMainModuleFrontController extends ModuleFrontController
{
	public $nbProducts;
	public $page_type;
	public $module_name = 'thnxblog';
	public static $initialized = false;
	public function __construct()
	{
	    parent::__construct();
	    $this->controller_type = 'modulefront';
	}
	public function init()
	{
		$this->page_type = $this->getPageType();
		$post_per_page = (int)Configuration::get(thnxblog::$thnxblogshortname."post_per_page");
		$this->n = (isset($post_per_page) && !empty($post_per_page)) ? $post_per_page : 12;
		if (self::$initialized) {
		    return;
		}
		self::$initialized = true;
	    parent::init();
	}
	public function initContent()
	{
		parent::initContent();
		$thnxblog = new thnxblog();
		$thnxblogsettings = $thnxblog->GetSettingsValueS();
		$column_use = Configuration::get(thnxblog::$thnxblogshortname."column_use");
		$this->context->smarty->assign(array(
			'thnxblog_column_use'  => $column_use,
		));
		// if($this->display_column_left && ($column_use == 'own_ps')){
		// 	$this->context->smarty->assign(array(
		// 	    'HOOK_LEFT_COLUMN'  => Hook::exec('displaythnxblogleft'),
		// 	));
		// }
		// if($this->display_column_right && ($column_use == 'own_ps')){
		// 	$this->context->smarty->assign(array(
		// 	    'HOOK_RIGHT_COLUMN'  => Hook::exec('displaythnxblogright'),
		// 	));
		// }
		$this->context->smarty->assign('thnxblogsettings',$thnxblogsettings);
	}
	public function setTemplate($template, $params = array(), $locale = null)
	{
	    if (!$path = $this->getTemplatePath($template)) {
	    	$themename = thnxblog::GetThemeName();
	        throw new PrestaShopException("thnxBlog ".$themename." Theme '$template' Template not found");
	    }
	    $this->template = $path;
	}
	public function getTemplatePath($template)
	{
		$themename = thnxblog::GetThemeName();
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
	public function getPageType(){
		$page_name = $this->context->controller->page_name;
		if(empty($page_name)){
			$page_type = 'category';
		}elseif(in_array($page_name,array('module-thnxblog-archive','thnxblog-thnxblog-module','thnxblog-archive-module','thnxblog-archive-aftrid-module','thnxblog-archive-wid-module'))){
			$page_type = 'category';
		}elseif(in_array($page_name, array('thnxblog-tag-module','thnxblog-tag-aftrid-module','thnxblog-tag-wid-module'))){
			$page_type = 'tag';
		}elseif(in_array($page_name, array('module-thnxblog-single','thnxblog-single-module','thnxblog-single-aftrid-module','thnxblog-single-wid-module'))){
			$page_type = 'post';
		}else{
			$page_type = 'category';
		}
		return $page_type;
	}
	public function getSubPageType(){
		$page_name = $this->context->controller->page_name;
		if(empty($page_name)){
			$sub_page_type = '';
		}elseif(in_array($page_name,array('thnxblog-thnxblog-module','thnxblog-archive-module','thnxblog-archive-aftrid-module','thnxblog-archive-wid-module','thnxblog-tag-module','thnxblog-tag-aftrid-module','thnxblog-tag-wid-module'))){
			$sub_page_type = 'post';
		}else{
			$sub_page_type = '';
		}
		return $sub_page_type;
	}
	public function pagination($total_products = null)
	{
	    if (!self::$initialized) {
	        $this->init();
	    } elseif (!$this->context) {
	        $this->context = Context::getContext();
	    }
	    // Retrieve the default number of products per page and the other available selections
	    $default_products_per_page = max(1, (int)Configuration::get(thnxblog::$thnxblogshortname."post_per_page"));
	    $n_array = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);
	    if ((int)Tools::getValue('n') && (int)$total_products > 0) {
	        $n_array[] = $total_products;
	    }
	    // Retrieve the current number of products per page (either the default, the GET parameter or the one in the cookie)
	    $this->n = $default_products_per_page;
	    if (isset($this->context->cookie->nb_item_per_page) && in_array($this->context->cookie->nb_item_per_page, $n_array)) {
	        $this->n = (int)$this->context->cookie->nb_item_per_page;
	    }
	    if ((int)Tools::getValue('n') && in_array((int)Tools::getValue('n'), $n_array)) {
	        $this->n = (int)Tools::getValue('n');
	    }
	    // Retrieve the page number (either the GET parameter or the first page)
	    $this->p = (int)Tools::getValue('p', 1);
	    // Remove the page parameter in order to get a clean URL for the pagination template
	    $current_url = preg_replace('/(?:(\?)|&amp;)p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));
	    if ($this->n != $default_products_per_page || isset($this->context->cookie->nb_item_per_page)) {
	        $this->context->cookie->nb_item_per_page = $this->n;
	    }
	    $pages_nb = ceil($total_products / (int)$this->n);
	    if ($this->p > $pages_nb && $total_products != 0) {
	        Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, $pages_nb, false));
	    }
	    $range = 2; /* how many pages around page selected */
	    $start = (int)($this->p - $range);
	    if ($start < 1) {
	        $start = 1;
	    }
	    $stop = (int)($this->p + $range);
	    if ($stop > $pages_nb) {
	        $stop = (int)$pages_nb;
	    }
	    $this->context->smarty->assign(array(
	        'nb_products'       => $total_products,
	        'products_per_page' => $this->n,
	        'pages_nb'          => $pages_nb,
	        'p'                 => $this->p,
	        'n'                 => $this->n,
	        'nArray'            => $n_array,
	        'range'             => $range,
	        'start'             => $start,
	        'stop'              => $stop,
	        'current_url'       => $current_url,
	    ));
	}
}