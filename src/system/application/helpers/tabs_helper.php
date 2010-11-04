<?php
/**
 * Helpers for creating Accessible Tabs.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Helpers
 * @author    Kathryn Reeve <kat@BinaryKitten.me.uk>
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */


class joindIn_TabContainer implements countable {
    /**
     * @var array Internal tab data storage
     **/
    private $_tabs = array();
    
    /**
     * @var string base name for this instance of tabs.
     **/
    private $_containerName = 'view';
    
    /**
     * @var string the hash id of the selected tab to display as primary
     **/
    private $_selectedTab = '';
    
    /**
     * @var string The Base URL from which tab links are built
     **/
    private $_baseUrl = '';
    
    /**
     * Set this tab instance's container name. Used as a base name for ids in the html render
     * @param string $name The New Container name
     * @return joindIn_Tabs Itself for a fluid chainable instance
     **/
    public function setContainerName($name) {
        $this->_containerName = $name;
        return $this;
    }
    
    /**
     * Get this tab instance's container name. Used as a base name for ids in the html render
     * @return string The Container Name
     **/
    public function getContainerName() {
        return $this->_containerName;
    }
    
    /**
     * Set this tab instance's Base URL. Used as a base for links in the html render
     * @param string $url The New Base URL
     * @return joindIn_Tabs Itself for a fluid chainable instance
     **/
    public function setBaseUrl($url) {
        $this->_baseUrl = $url;
        return $this;
    }
    
    /**
     * Get this tab instance's Base URL. Used as a base for links in the html render
     * @return string The Base URL
     **/
    public function getBaseUrl() {
        return $this->_baseUrl;
    }
    
    /**
     * Set this tab instance's Selected Tab. 
     * @param string $hash the hash of the tab to make selected
     * @return joindIn_Tabs Itself for a fluid chainable instance
     **/
    public function setSelectedTab($hash) {
        $this->_selectedTab = $hash;
        return $this;
    }
    
    /**
     * Get this tab instance's selected Tab.
     *      Please note, this does not get active on client side.
     * @return string The Active Tab's Hash
     **/
    public function getSelectedTab() {
        return $this->_selectedTab;
    }
    
    /**
     * Add a Tab
     *
     * Adds a new tab to the collection
     ******************************************** 
     * @return string ID of tab that was just added
     **/
    public function addTab(joindIn_Tab $tab, $id=null) {
        $this->_tabs[md5($content.$hash)] = array(
          'url'     =>$url,
          'caption' =>$caption,
          'content' =>$content,
          'hash'    =>$hash
        );
        return $id;
    }
    
    public function render() {
        if ($this->count() === 0) {
            return '';
        }
        ob_start();
        $contentList = array();
        reset($this->_tabs);
        if (empty($this->_selectedTab)) {
            $tmp_tab = current($this->_tabs);
            $this->_selectedTab = $tmp_tab['hash'];
            reset($this->_tabs);
        }
        ?>
        <div id="<?php echo $this->_containerName; ?>-tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
        <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
            <?php foreach($this->_tabs as $tab): ?>
            <li class="ui-state-default ui-corner-top<?php if($tab['hash'] == $this->_selectedTab){?> ui-tabs-selected ui-state-active ui-state-focus<?php } ?>">
                <a href="<?php echo $this->_baseUrl, $tab['url'],'#'.$this->_containerName.'-tabs'; ?>" rel="<?php echo $tab['hash']; ?>"><?php echo $tab['caption']; ?></a>
            </li>
            <?php
            $contentList[$tab['hash']] = $tab['content'];
            endforeach; ?>
        </ul>
        <?php foreach($contentList as $hash=>$content): 
            $tabID = substr($hash,1) ?>
            
        <?php endforeach; ?>
        </div>
        <?php $content = ob_get_clean();
        return $content;
    }
    
    
    public function __toString() {
        return $this->render();
    }
    /**
     * Countable Interface
     * @return int the amout of tabs that we have
     **/
    public function count() {
        return count($this->_tabs);
    }
}

/*
 * joindIn_Tab
 *
 * A Singular Tab to be added to the Tab Container
 */
class joindIn_Tab {
    
    private $_id                        = '';
    private $_url                       = '';
    private $_selected                  = false;
    private $_caption                   = '';
    private $_content                   = '';
    private $_parentId                  = '';
    
    private $_headerClassses            = array(
        'ui-state-default',
        'ui-corner-top'
    );
    
    private $_contentClasses            = array(
        'ui-tabs-panel',
        'ui-widget-content',
        'ui-corner-bottom'
    );
    
    private $_selectedHeaderClasses     = array(
        'ui-tabs-selected',
        'ui-state-active',
        'ui-state-focus'
    );
    
    private $_selectedContentClasses    = array();
    private $_hiddenContentClasses      = array('ui-tabs-hide');
    
    private $_headerFormat      = '<li class="%1$s"><a href="%2$s#%3$s-tabs" rel="%4$s">%5$s</a></li>';
    private $_contentFormat     = '<div class="%1$s" id="%2$s"><div class="ui-widget">%3$s</div></div>';
    
    /**
     * Constructor
     *
     * @param string $url The URL of the tab link
     * @param string $caption The caption of the tab
     * @param string $content the content of the tab
     * @param boolean $selected Whether this tab is selected
     **/
    public function __construct($url='', $caption='', $content='', $selected=false) {
        $this->caption  = $caption;
        $this->selected = $selected;
        $this->caption  = $caption;
        $this->content  = $content;
    }

    public function clearContent() {
        $this->content = '';
        return $this;
    }
    
    public function addContent($content) {
        $this->_content .= $content;
        return this;
    }
    
    public function getContent() {
        return $this->_content;
    }
    
    public function setContent($content) {
        $this->_content = $content;
        return $this;
    }
    
    public function setId($newId) {
        $this->_id = $newId;
        return $this;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setCaption($newCaption) {
        $this->_caption = $newCaption;
        return $this;
    }
    
    public function setParentId($newParentId) {
        $this->_parentId = $newParentId;
        return $this;
    }
    public function render() {
        $header     = '';
        $content    = '';
        
        if (empty($this->_id)) {
            $this->_id = md5($this);
        }
        $classes = $this->_headerClassses;
        if ($this->_selected) {
            $classes = array_merge($classes, $this->_selectedHeaderClasses);
        }
        $classText = implode(' ', $classes);
        $header = sprintf($this->_headerFormat, $classText, $this->_url, $this->_parentId, $this->_id, $this->_caption);
        
        
        $classes = $this->_contentClasses;
        if (!$this->_selected) {
            $classes = array_merge($classes, $this->_hiddenContentClasses);
        }
        $classText = implode(' ', $classes);
        $content = sprintf($this->_contentFormat, $classText, $this->_id, $this->_content);
        
        return array($header, $content);
    }
    
    public function __toString() {
        list($header, $content) = $this->render();
        return $header.$content;
    }
}

