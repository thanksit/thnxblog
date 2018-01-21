<?php
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;
class thnxblogArchiveModuleFrontController extends thnxblogMainModuleFrontController
{
	public $blogpost;
	public $blogcategory;
	public $thnxerrors;
	public $id_identity;
	public $rewrite;
    public function init()
	{
        parent::init();
       	$this->rewrite = Tools::getValue('rewrite');
       	$subpage_type = $this->getSubPageType();
       	$p = Tools::getValue('page');
		$this->p = isset($p) && !empty($p) ? $p : 1;
		$id_identity = Tools::getValue('id');
		if(!isset($id_identity) || empty($id_identity)){
			$this->id_identity = (int)thnxcategoryclass::get_the_id($this->rewrite,$this->page_type);
		}else{
			$this->id_identity = (int)$id_identity;
		}
		if(isset($this->id_identity) && !empty($this->id_identity) && !thnxcategoryclass::CategoryExists($this->id_identity,$this->page_type)){
			$url = thnxblog::thnxBlogLink();
			Tools::redirect($url);
			$this->thnxerrors[] = Tools::displayError($this->l('Blog Category Not Found.' ));
		}
        if($this->page_type == 'tag'){
        	$this->blogpost = thnxpostsclass::GetTagPosts((int)$this->id_identity,(int)$this->p,(int)$this->n,$subpage_type);
        }else{
        	$this->blogpost = thnxpostsclass::GetCategoryPosts((int)$this->id_identity,(int)$this->p,(int)$this->n,$subpage_type);
        }
        if($this->id_identity || Validate::isUnsignedId($this->id_identity)){
        	$this->blogcategory = new thnxcategoryclass($this->id_identity);
        }
		$this->nbProducts = (int)thnxpostsclass::GetCategoryPostsCount((int)$this->id_identity,$subpage_type);
    }
    public function setMedia()
    {
        parent::setMedia();
        $themename = thnxblog::GetThemeName();
        $theme_name = (isset($themename) && !empty($themename)) ? '/'.$themename : '';
        $this->addCSS(thnxblog_css_uri.$theme_name.'css/thnxblog_archive.css');
        $this->addJS(thnxblog_js_uri.$theme_name.'js/thnxblog_archive.js');
    }
    public function initContent()
	{
        parent::initContent();
        // print_r($this->getLayout());
        $id_lang = (int)Context::getContext()->language->id;
		$pagination = $this->getthnxPagination();
		$path = thnxcategoryclass::getcategorypath($this->id_identity,$this->page_type);
		$this->context->smarty->assign('path',$path);
		$this->context->smarty->assign('pagination',$pagination);
        if(isset($this->blogpost) && !empty($this->blogpost)){
        	$this->context->smarty->assign('thnxblogpost',$this->blogpost);
        }
		if(isset($this->blogcategory->title[$id_lang]) && !empty($this->blogcategory->title[$id_lang])){
			$this->context->smarty->assign('meta_title',$this->blogcategory->title[$id_lang]);
			$this->context->smarty->tpl_vars['page']->value['meta']['title'] = $this->blogcategory->title[$id_lang];
		}else{
			$this->context->smarty->assign('meta_title',Configuration::get(thnxblog::$thnxblogshortname."meta_title"));
			$this->context->smarty->tpl_vars['page']->value['meta']['title'] = Configuration::get(thnxblog::$thnxblogshortname."meta_title");
		}
		if(isset($this->blogcategory->meta_description[$id_lang]) && !empty($this->blogcategory->meta_description[$id_lang])){
			$this->context->smarty->assign('meta_description',$this->blogcategory->meta_description[$id_lang]);
		}else{
			$this->context->smarty->assign('meta_description',Configuration::get(thnxblog::$thnxblogshortname."meta_description"));
		}
		if(isset($this->blogcategory->keyword[$id_lang]) && !empty($this->blogcategory->keyword[$id_lang])){
			$this->context->smarty->assign('meta_keywords',$this->blogcategory->keyword[$id_lang]);
		}else{
			$this->context->smarty->assign('meta_keywords',Configuration::get(thnxblog::$thnxblogshortname."meta_keyword"));
		}
     	if(isset($this->thnxerrors) && !empty($this->thnxerrors)){
        	$this->context->smarty->assign('thnxerrors',$this->thnxerrors);
        }

        $tpl_prefix = '';
        $template = 'archive.tpl';
        if(!empty($this->page_type)){
        	$template1 = $this->page_type.'-'.'archive.tpl';
        	if ($path = $this->getTemplatePath($template1)) {
        		$template = $template1;
        	}else{
        		$template = 'archive.tpl';
        	}
        }
        $this->setTemplate($template);
    }
    public function getLayout()
    {
        $entity = 'module-thnxblog-archive';
        $layout = $this->context->shop->theme->getLayoutRelativePathForPage($entity);
        if ($overridden_layout = Hook::exec(
            'overrideLayoutTemplate',
            array(
                'default_layout' => $layout,
                'entity' => $entity,
                'locale' => $this->context->language->locale,
                'controller' => $this,
            )
        )) {
            return $overridden_layout;
        }
        if ((int) Tools::getValue('content_only')) {
            $layout = 'layouts/layout-content-only.tpl';
        }
        return $layout;
    }
    public function updatethnxQueryString(array $extraParams = null)
    {
        $uriWithoutParams = explode('?', $_SERVER['REQUEST_URI'])[0];
        $url = Tools::getCurrentUrlProtocolPrefix().$_SERVER['HTTP_HOST'].$uriWithoutParams;
        $params = array();
        parse_str($_SERVER['QUERY_STRING'], $params);

        if (null !== $extraParams) {
            foreach ($extraParams as $key => $value) {
                if (null === $value) {
                    unset($params[$key]);
                } else {
                    $params[$key] = $value;
                }
            }
        }

        ksort($params);

        if (null !== $extraParams) {
            foreach ($params as $key => $param) {
                if (null === $param || '' === $param) {
                    unset($params[$key]);
                }
            }
        } else {
            $params = array();
        }

        $queryString = str_replace('%2F', '/', http_build_query($params));

        return $url.($queryString ? "?$queryString" : '');
    }
    public function getthnxPagination() {

        $pagination = new Pagination();
        $pagination
            ->setPage($this->p)
            ->setPagesCount(
                ceil($this->nbProducts / $this->n)
            )
        ;
        $totalItems = $this->nbProducts;
        $itemsShownFrom = ($this->n * ($this->p - 1)) + 1;
        $itemsShownTo = $this->n * $this->p;
        return array(
            'total_items' => $totalItems,
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
            'pages' => array_map(function ($link) {
                $link['url'] = $this->updatethnxQueryString(array(
                    'page' => $link['page'],
                ));
                return $link;
            }, $pagination->buildLinks()),
        );
    }
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $blog_title = Configuration::get(thnxblog::$thnxblogshortname."meta_title");
        $breadcrumb['links'][] = array(
            'title' => $blog_title,
            'url' => thnxblog::thnxBlogLink(),
        );
        $id_lang = (int)$this->context->language->id;

        if(isset($this->blogcategory->title[$id_lang]) && !empty($this->blogcategory->title[$id_lang])){
        	$category_name = $this->blogcategory->title[$id_lang];
        }elseif(isset($this->blogcategory->name[$id_lang]) && !empty($this->blogcategory->name[$id_lang])){
        	$category_name = $this->blogcategory->name[$id_lang];
        }else{
        	$category_name = '';
        }
        $params = array();
        $params['id'] = $this->blogcategory->id_thnxcategory ? $this->blogcategory->id_thnxcategory : 0;
        $params['rewrite'] = (isset($this->blogcategory->link_rewrite[$id_lang]) && !empty($this->blogcategory->link_rewrite[$id_lang])) ? $this->blogcategory->link_rewrite[$id_lang] : 'category_blog_post';
		$category_url = thnxblog::thnxBlogCategoryLink($params);
        $breadcrumb['links'][] = array(
            'title' => $category_name,
            'url' => $category_url,
        );
        return $breadcrumb;
    }
}