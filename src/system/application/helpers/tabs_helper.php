<?php
/**
 * Helpers for creating Accessible Tabs.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Helpers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/**
 * Helpers for creating Accessible Tabs.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Helpers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */
class joindIn_TabContainer implements countable
{
    /**
     * @var array Internal tab data storage
     */
    private $_tabs = array();
    
    /**
     * @var string base name for this instance of tabs.
     */
    private $_containerName = 'view';
    
    /**
     * @var string the hash id of the selected tab to display as primary
     */
    private $_selectedTab = '';
    
    /**
     * @var string The Base URL from which tab links are built
     */
    private $_baseUrl = '';
    
    /**
     * Set this tab instance's container name. Used as a base name for ids 
     * in the html render
     * 
     * @param string $name The New Container name
     *
     * @return joindIn_Tabs Itself for a fluid chainable instance
     */
    public function setContainerName($name) 
    {
        $this->_containerName = $name;
        return $this;
    }
    
    /**
     * Get this tab instance's container name. Used as a base name for ids 
     * in the html render
     *
     * @return string The Container Name
     */
    public function getContainerName() 
    {
        return $this->_containerName;
    }
    
    /**
     * Set this tab instance's Base URL. Used as a base for links in the 
     * html render
     *
     * @param string $url The New Base URL
     *
     * @return joindIn_Tabs Itself for a fluid chainable instance
     */
    public function setBaseUrl($url) 
    {
        $this->_baseUrl = $url;
        return $this;
    }
    
    /**
     * Get this tab instance's Base URL. Used as a base for links in the 
     * html render
     *
     * @return string The Base URL
     */
    public function getBaseUrl() 
    {
        return $this->_baseUrl;
    }
    
    /**
     * Set this tab instance's Selected Tab. 
     *
     * @param string $id Id of the tab to make selected
     *
     * @return joindIn_Tabs Itself for a fluid chainable instance
     */
    public function setSelectedTab($id) 
    {
        
        if (isset($this->_tabs[$id])) {
            $this->_tabs[$id]->setSelected();
        }
        $this->_selectedTab = $id;
        return $this;
    }
    
    /**
     * Get this tab instance's selected Tab.
     *      Please note, this does not get active on client side.
     *
     * @return string The Active Tab's Hash
     */
    public function getSelectedTab() 
    {
        return $this->_tabs[$this->_selectedTab];
    }
    
    /**
     * Adds a new tab to the collection. If you specify an id it will
     * use that in the collection, otherwise it will use the tab's own
     * id
     *
     * @param joindIn_Tab $tab Tab to add
     * @param string      $id  Optional id.
     *
     * @return string ID of tab that was just added
     */
    public function addTab(joindIn_Tab $tab, $id=null) 
    {
        if ($id===null) {
            $id = $tab->getId();
        }
        $this->_tabs[$id] = $tab;
        return $id;
    }
    
    /**
     * Sets the tabs in the collection
     *
     * @param array $tabs Tabs to set
     *
     * @return null
     */
    public function setTabs(array $tabs) 
    {
        $this->_tabs = $tabs;
    }
    

    /**
     * Retrieves the tabs in the collection
     *
     * @return array
     */
    public function getTabs() 
    {
        return $this->_tabs;
    }
    
    /**
     * Retrieves a tab by its id
     *
     * @param string $id Tab id to fetch
     *
     * @return joindIn_Tab|null
     */
    public function getTab($id) 
    {
        if (isset($this->_tabs[$id])) {
            return $this->_tabs[$id];
        }
        return null;
    }
    
    /**
     * Adds tabs to the collection
     *
     * @param array $tabs Tabs to add to collection
     *
     * @return null
     */
    public function addTabs(array $tabs) 
    {
        if (count($this->_tabs) > 0) {
            $this->_tabs = array_merge($this->_tabs, $tabs);
        } else {
            $this->_tabs = $tabs;
        }
    }
    
    /**
     * Renders a tab container
     *
     * @return string
     */
    public function render() 
    {
        if ($this->count() === 0) {
            return '';
        }
        ob_start();
        $contentList = array();
        reset($this->_tabs);
        if (empty($this->_selectedTab) 
            || !isset($this->_tabs[$this->_selectedTab])
        ) {
            $tmp_tab            = current($this->_tabs);
            $this->_selectedTab = $tmp_tab->getId();
            reset($this->_tabs);
        }
        ?>
        <div id="<?php echo $this->_containerName; ?>-tabs" class="ui-tabs 
            ui-widget ui-widget-content ui-corner-all">
        <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix
             ui-widget-header ui-corner-all">
            <?php
        foreach ($this->_tabs as $tab) {
            if ($tab->getId() == $this->_selectedTab) {
                    $tab->setSelected();
            }
            $tab->setParentId($this->_containerName);
            $tab->setBaseUrl($this->_baseUrl);
            list($tabTop, $content) = $tab->render();
            echo $tabTop;
            $contentList[$tab->getId()] = $content;
        }
        ?>
        </ul>
        <?php 
        foreach ($contentList as $hash=>$tabContent) {
            echo $tabContent;
        }
        ?>
        </div>
        <?php $content = ob_get_clean();
        return $content;
    }
    
    /**
     * Returns a string representation of a tab container
     *
     * @return string
     */
    public function __toString() 
    {
        return $this->render();
    }

    /**
     * Countable Interface
     *
     * @return int the amount of tabs that we have
     */
    public function count() 
    {
        return count($this->_tabs);
    }
}

/**
 * joindIn_Tab
 *
 * A Singular Tab to be added to the Tab Container
 *
 * @category Tab
 * @package  Helper
 * @license  http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 */
class joindIn_Tab
{
    private $_id       = '';
    private $_url      = '';
    private $_baseURL  = '';
    private $_selected = false;
    private $_caption  = '';
    private $_content  = '';
    private $_parentId = '';
    
    private $_headerClassses = array(
        'ui-state-default',
        'ui-corner-top'
    );
    
    private $_contentClasses = array(
        'ui-tabs-panel',
        'ui-widget-content',
        'ui-corner-bottom'
    );
    
    private $_selectedHeaderClasses = array(
        'ui-tabs-selected',
        'ui-state-active',
        'ui-state-focus'
    );
    
    private $_selectedContentClasses = array();
    private $_hiddenContentClasses   = array('ui-tabs-hide');
    
    private $_headerFormat;
    private $_contentFormat;
    
    /**
     * Constructor
     *
     * @param string  $url      The URL of the tab link
     * @param string  $caption  The caption of the tab
     * @param string  $content  the content of the tab
     * @param boolean $selected Whether this tab is selected
     */
    public function __construct(
        $url      = '', 
        $caption  = '', 
        $content  = '', 
        $selected = false
    ) {
        $this->_id       = $url;
        $this->_url      = $url;
        $this->_selected = $selected;
        $this->_caption  = $caption;
        $this->_content  = $content;
        
        $this->_headerFormat  = '<li class="%1$s">'.
            '<a href="%2$s#%3$s-tabs" rel="%4$s">%5$s</a></li>';
        $this->_contentFormat = '<div class="%1$s" id="%2$s">'.
            '<div class="ui-widget">%3$s</div></div>';
    }

    /**
     * Clears the content of this tab. This method is chainable.
     *
     * @return joindIn_TabContainer
     */
    public function clearContent() 
    {
        $this->content = '';
        return $this;
    }
    
    /**
     * Appends content to the tab. This method is chainable.
     *
     * @param string $content Content to add to tab
     *
     * @return joindIn_TabContainer
     */
    public function addContent($content) 
    {
        $this->_content .= $content;
        return $this;
    }
    
    /**
     * Returns the contents for this tab.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }
    
    /**
     * Sets the content for this tab. This method is chainable.
     *
     * @param string $content Content to set for tab
     *
     * @return joindIn_TabContainer
     */
    public function setContent($content) 
    {
        $this->_content = $content;
        return $this;
    }
    
    /**
     * Sets the id of this tab. This method is chainable.
     *
     * @param string $newId New Id for this tab
     *
     * @return joindIn_TabContainer
     */
    public function setId($newId) 
    {
        $this->_id = $newId;
        return $this;
    }
    
    /**
     * Retrieves the id for the tab
     *
     * @return int
     */
    public function getId() 
    {
        return $this->_id;
    }
    
    /**
     * Sets the caption for the tab. This method is chainable.
     *
     * @param string $newCaption New caption for the tab
     *
     * @return joindIn_TabContainer
     */
    public function setCaption($newCaption) 
    {
        $this->_caption = $newCaption;
        return $this;
    }

    /**
     * Retrieves the caption for the tab.
     *
     * @return string
     */
    public function getCaption() 
    {
        return $this->_caption;
    }

    /**
     * Sets the parent id of the tab. This method is chainable.
     *
     * @param int $newParentId New Parent ID to set
     *
     * @return joindIn_TabContainer
     */
    public function setParentId($newParentId) 
    {
        $this->_parentId = $newParentId;
        return $this;
    }

    /**
     * Retrieves the url for the tab
     *
     * @return string
     */
    public function getURL() 
    {
        return $this->_url;
    }

    /**
     * Sets the base url for the tab. This method is chainable.
     *
     * @param string $newBaseURL New URL to set as base
     *
     * @return joindIn_TabContainer
     */
    public function setBaseURL($newBaseURL) 
    {
        $this->_baseURL = $newBaseURL;
        return $this;
    }

    /**
     * Retrieves the base url for the tab
     *
     * @return string
     */
    public function getBaseURL() 
    {
        return $this->_baseURL;
    }

    /**
     * Sets the tab as selected. This method is chainable.
     *
     * @param boolean $selected Whether the tab is selected
     *
     * @return joindIn_TabContainer
     */
    public function setSelected($selected=true) 
    {
        $this->_selected = $selected;
        return $this;
    }

    /**
     * Returns the selected tab
     *
     * @return boolean
     */
    public function getSelected() 
    {
        return $this->_selected;
    }

    /**
     * Renders some tabs
     *
     * @param string $headerFormat  Format string for header
     * @param string $contentFormat Format string for content
     *
     * @return array
     */
    public function render($headerFormat = '', $contentFormat = '') 
    {
        $header = '';
        if (empty($headerFormat)) {
            $headerFormat = $this->_headerFormat;
        }
        $content = '';
        if (empty($contentFormat)) {
            $contentFormat = $this->_contentFormat;
        }
        
        if (empty($this->_id)) {
            $this->_id = md5($this);
        }
        $classes = $this->_headerClassses;
        if ($this->_selected) {
            $classes = array_merge($classes, $this->_selectedHeaderClasses);
        }
        $classText = implode(' ', $classes);
        $header    = sprintf(
            $headerFormat, 
            $classText, 
            $this->_baseURL.$this->_url, 
            (!empty($this->_parentId) ? $this->_parentId : $this->_id), 
            $this->_id, 
            $this->_caption
        );
        
        $classes = $this->_contentClasses;
        if (!$this->_selected) {
            $classes = array_merge($classes, $this->_hiddenContentClasses);
        }
        $classText = implode(' ', $classes);
        $content   = sprintf(
            $contentFormat, 
            $classText, 
            $this->_id, 
            $this->_content
        );
        
        return array($header, $content);
    }
    
    /**
     * Returns a string representation of tabs
     *
     * @return string 
     */
    public function __toString() 
    {
        list($header, $content) = $this->render();
        return $header.$content;
    }
}

