<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Table tl_phpsass
 */
$GLOBALS['TL_DCA']['tl_phpsass'] = array
(
    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'enableVersioning'            => true,
    ),
    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 2,
            'fields'                  => array('name'),
            'flag'                    => 1,
            'panelLayout'             => 'sort,search,limit'
        ),
        'label' => array
        (
            'fields'                  => array('name'),
            'format'                  => '%s',
            'label'                   => &$GLOBALS['TL_LANG']['tl_phpsass']['name'],
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_phpsass']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'toggle' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_phpsass']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('tl_phpsass', 'toggleIcon')
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_phpsass']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default'                     => '{title_legend},name;{config_legend},sass_dir,css_dir,extensions_dir,images_dir,javascripts_dir,output_style;disable'
    ),

    // Fields
    'fields' => array
    (
        'name' => array(
          'label'                     => &$GLOBALS['TL_LANG']['tl_phpsass']['name'],
          'inputType'                 => 'text',
          'exclude'                 => true,
          'sorting'                 => true,
          'flag'                    => 1,
          'search'                  => true,
          'eval'                    => array('mandatory'=>true, 'unique'=>true, 'decodeEntities'=>true, 'maxlength'=>128, 'tl_class'=>'w50')
        ),
        'sass_dir' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_phpsass']['sass_dir'],
            'inputType'               => 'fileTree',
            'eval'                    => array('mandatory'=>true, 'fieldType' => 'radio')
        ),
        'css_dir' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_phpsass']['css_dir'],
            'inputType'               => 'fileTree',
            'eval'                    => array('mandatory'=>true, 'fieldType' => 'radio')
        ),
        'extensions_dir' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_phpsass']['extensions_dir'],
            'inputType'               => 'fileTree',
            'eval'                    => array('mandatory'=>false, 'fieldType' => 'radio')
        ),
        'images_dir' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_phpsass']['images_dir'],
            'inputType'               => 'fileTree',
            'eval'                    => array('mandatory'=>false, 'fieldType' => 'radio')
        ),
        'javascripts_dir' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_phpsass']['javascripts_dir'],
            'inputType'               => 'fileTree',
            'eval'                    => array('mandatory'=>false, 'fieldType' => 'radio')
        ),
        'output_style' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_phpsass']['output_style'],
            'inputType'               => 'select',
            'options_callback'        => array('tl_phpsass','getOutputStyleOptions'),
            'eval'                    => array('mandatory'=>true,'fieldType' => 'select')
        ),
        'disable' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_phpsass']['disable'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox'
        ),
    )
);

class tl_phpsass extends Backend
{
    public function __construct()
    {
        parent::__construct();
        $this->import('Database');
        $this->import('BackendUser', 'User');
    }

    public function getOutputStyleOptions()
    {
        return array(
            'expanded' => &$GLOBALS['TL_LANG']['tl_phpsass']['output_style_expanded'],
            'nested' => &$GLOBALS['TL_LANG']['tl_phpsass']['output_style_nested'],
            'compact' => &$GLOBALS['TL_LANG']['tl_phpsass']['output_style_compact'],
            'compressed' => &$GLOBALS['TL_LANG']['tl_phpsass']['output_style_compressed'],
        );
    }

    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen($this->Input->get('tid')))
        {
            $this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_phpsass::disable', 'alexf'))
        {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.$row['disable'];

        if ($row['disable'])
        {
            $icon = 'invisible.gif';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
    }

    public function toggleVisibility($intId, $blnVisible)
    {
        // Check permissions
        if (!$this->User->isAdmin && !$this->User->hasAccess('tl_phpsass::disable', 'alexf'))
        {
            $this->log('Not enough permissions to activate/deactivate phpsass ID "'.$intId.'"', 'tl_phpsass toggleVisibility', TL_ERROR);
            $this->redirect('contao/main.php?act=error');
        }

        $this->createInitialVersion('tl_phpsass', $intId);

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_phpsass']['fields']['disable']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_phpsass']['fields']['disable']['save_callback'] as $callback)
            {
                $this->import($callback[0]);
                $blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
            }
        }

        $time = time();

        // Update the database
        $this->Database->prepare("UPDATE tl_phpsass SET tstamp=$time, disable='" . ($blnVisible ? '' : 1) . "' WHERE id=?")
            ->execute($intId);

        $this->createNewVersion('tl_phpsass', $intId);
    }
}
