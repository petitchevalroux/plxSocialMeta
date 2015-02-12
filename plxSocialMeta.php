<?php

class plxSocialMeta extends plxPlugin
{

    /**
     * Constructeur de la classe
     * @param string $default_lang langue par defaut.
     * */
    public function __construct($default_lang)
    {
        parent::__construct($default_lang);
        $this->setConfigProfil(PROFIL_ADMIN);
        $this->addHook('ThemeEndHead', 'ThemeEndHead');
    }

    /**
     * Retourne le contenu du titre de l'article
     * @param plxShow $plxShow
     * @return string
     */
    private function articleTitle($plxShow)
    {
        ob_start();
        $plxShow->pageTitle();
        return html_entity_decode(ob_get_clean());
    }

    /**
     * Retourne le contenu de l'url de la première image de l'article
     * @param plxShow $plxShow
     * @return string
     */
    private function imageUrl($plxShow)
    {
        $image = '';
        ob_start();
        $plxShow->artContent();
        $artContent = ob_get_clean();
        if (preg_match('~<img[^>]*?src="(.*?)"[^>]+>~', $artContent, $match)) {
            $image = trim($match[1]);
            if (strpos($image, 'http') !== 0) {
                $image = 'http://' . $_SERVER['SERVER_NAME'] . '/' . trim($match[1]);
            }
        }
        return $image;
    }

    /**
     * Retourne le contenu de la description de l'article
     * @param plxShow $plxShow
     * @return string
     */
    private function articleDescription($plxShow)
    {
		ob_start();
		$plxShow->meta('description');
		$plxdescription = ob_get_clean();
		preg_match("/<meta[^>]*name=[\"|\']description[\"|\'][^>]*content=[\"]([^\"]*)[\"][^>]*>/i", $plxdescription, $description);
		//echo "<pre>"; var_dump(array_filter($description)); echo "</pre>"; exit;
		return html_entity_decode($description[1]);
    }

    /**
     * Retourne le contenu de l'url de l'article
     * @param plxShow $plxShow
     * @return string
     */
    public function articleUrl($plxShow)
    {
        ob_start();
        $plxShow->artUrl();
        return ob_get_clean();
    }

    /**
     * Retourne le nom de l'auteur
     * @param plxShow $plxShow
     * @return string
     */
    public function authorName($plxShow)
    {
        return $plxShow->artAuthor(false);
    }

    /**
     * Hook exécuté à la fin de la balise head
     */
    public function ThemeEndHead()
    {
        $plxShow = plxShow::getInstance();
        if ($plxShow->plxMotor->mode === 'article') {
            $metas = $this->getMetas();
            echo $this->replaceTag($metas);
            //Si on a une microdonnée schema.org dans les metas
            if ($this->getParam('schemaorg_enabled')) {
                //On ajoute le scope et le type de l'item à la balise <html>
                //uniquement si ils ne sont pas présents
                echo '<?php'
                . ' if (preg_match(\'~<html([^>]*)>~\', $output, $matches)'
                . '&& strpos($matches[1], \'itemscope\') === false'
                . '&& strpos($matches[1], \'itemtype="\') === false) {
                            $output = str_replace($matches[0],
                            \'<html\' . $matches[1] . \' itemscope itemtype="http://schema.org/Article">\',
                            $output);}?>';
            }
        }
    }

    /**
     * Remplace les tags par leurs valeurs
     * @param string $metas
     * @return string
     */
    public function replaceTag($metas)
    {
        $plxShow = plxShow::getInstance();
        $searchTags = array(
            'articleUrl',
            'articleTitle',
            'articleDescription',
            'imageUrl',
            'authorName'
        );
        $search = array();
        $replace = array();
        foreach ($searchTags as $searchTag) {
            $formatedSearchTag = '%' . $searchTag . '%';
            if (strpos($metas, $formatedSearchTag) !== false) {
                $search[] = $formatedSearchTag;
                $replace[] = plxUtils::strCheck(call_user_func(array($this, $searchTag), $plxShow));
            }
        }
        if (empty($search)) {
            return $metas;
        }
        return str_replace($search, $replace, $metas);
    }

    /**
     * Retourne les metas à insérer
     * @return string
     */
    private function getMetas()
    {
        $metas = '';
        if ($this->getParam('opengraph_enabled')) {
            $metas .= $this->getOpengraphMetas();
        }
        if ($this->getParam('schemaorg_enabled')) {
            $metas .= $this->getSchemaOrgMetas();
        }
        if ($this->getParam('twittercard_enabled')) {
            $metas .= $this->getTwitterCardMetas();
        }
        return $metas;
    }

    /**
     * Retourne les metas opengraph pour Facebook et Google Plus
     * Doc :
     * https://developers.facebook.com/docs/opengraph
     *
     * Validateur :
     * https://developers.facebook.com/tools/debug
     * https://developers.google.com/+/web/snippet/
     * @return string
     */
    private function getOpengraphMetas()
    {
        $metas = '';
        $opengraph = array(
            'type' => 'article',
            'url' => '%articleUrl%',
            'title' => '%articleTitle%',
            'description' => '%articleDescription%',
            'image' => '%imageUrl%'
        );
        foreach ($opengraph as $property => $content) {
            $metas .= '<meta property="og:'
                    . plxUtils::strCheck($property)
                    . '" content="' . $content . '"/>';
        }
        return $metas;
    }

    /**
     * Retourne les metas twitter card
     * Doc :
     * https://dev.twitter.com/cards/types/summary-large-image
     * Validateur :
     * https://cards-dev.twitter.com/validator
     * 
     * @return string
     */
    private function getTwitterCardMetas()
    {
        $metas = '';
        $twitter = array(
            'url' => '%articleUrl%',
            'title' => '%articleTitle%',
            'description' => '%articleDescription%',
            'image:src' => '%imageUrl%'
        );
        foreach ($twitter as $property => $content) {
            $metas .= '<meta name="twitter:'
                    . plxUtils::strCheck($property)
                    . '" content="' . $content . '"/>';
        }
        $type = $this->getParam('twittercard_type');
        if (!empty($type)) {
            $metas .= '<meta name="twitter:card" content="' . plxUtils::strCheck($type) . '"/>';
        }

        $site = $this->getParam('twittercard_site');
        if (!empty($site)) {
            $metas .= '<meta name="twitter:site" content="@' . plxUtils::strCheck($site) . '"/>';
        }
        return $metas;
    }

    /**
     * Retourne les metas schema.org pour Google Plus
     * Doc :
     * https://developers.google.com/+/web/snippet/
     *
     * Validateur :
     * http://www.google.com/webmasters/tools/richsnippets
     * @return string
     */
    private function getSchemaOrgMetas()
    {
        $metas = '';
        $schemaorg = array(
            'name' => '%articleTitle%',
            'description' => '%articleDescription%',
            'image' => '%imageUrl%',
            'url' => '%articleUrl%'
        );
        foreach ($schemaorg as $property => $content) {
            $metas .= '<meta itemprop="'
                    . plxUtils::strCheck($property)
                    . '" content="' . $content . '"/>';
        }
        return $metas;
    }

}
