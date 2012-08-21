<?php
namespace Mazagran\Translation\Diagnostics;

use Nette;
use Nette\Diagnostics\Debugger;
use Nette\Utils\PhpGenerator as Code;

/**
 * Mazagran Translation Panel
 *
 * @author martin.bazik
 */
class Panel extends Nette\Object implements Nette\Diagnostics\IBarPanel
{
	
	/**
	 * Renders HTML code for custom tab.
	 * @return string
	 */
	public function getTab()
	{
		return '<span title="Mazagran Translation">' .
			'<img alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAJvSURBVDiNpZJPSJRBGIefme9zd1tZZTHdLF1JLA9R0EUNXJbs3LlDl/LQNRD6R8mS1KEOYV76RyAUVKegIOjQpbAlq5MnSzGLytXWqHXX3W+/mbfD5upitwZemJnDM7/3mVeJCP+z3LXN0NDQFtfVR0Oh4GlBYlqpxdXV4gWllAmGghettTHH0Zmy518NBsOPUqlUEUCJCOeHz45ppQcTiaTT13sgFIlEyOVyTLx+VfI+zuqBY8fr6uvryRfyvH//rpROT/i+MXeHz6dOkuzunz977pQYY2RtGWMkX8jLykpOng0clLmZD5LJZGRpaUkKhYL4vi+XLo9Isrt/XotIXCmF1rral4jgeSW+fP3MYlsrc+PjWGspFotks1m01riug4jENYBSqkaMiFCRK/i9Pfy4d5+50dFaea67LtFxnM0AK1grSChI2/VrfDpyFB0Os2NwsAagNx4ArLUVQAWFtZZAPE7biRPMjIxQnp1l46MaQOv1BGvxRWwNNJpIgDEU3rz5V4J/AQT7d8jECoFYDICV6emaBJscOI5TKTdKqVRkV9du9uzZi1v2eQsU/gJqJLquizGGjzMfmJycpOyVaG+P09PTx769+0mnX/Pt21ccYHlqCs/zqj9XtXf7zg1i21ro7OpAac3vX7958vQxSikaow00tzTxs7mZolgePLyPMWYdEI+3s7Ork0CgrtpKY2MD27e3YoypVvjWGMYIpilKdnF5HfD85kvgJQBeucRC9nPOdep069aOeoDvP+bzvinbbU3xSKAuuHFkUMnu/gURiW24KynUGaXUISv2MIBW+qmIvBDkClAlKKUyfwCFfzzBSyXaZgAAAABJRU5ErkJggg==" />'
			.'</span>';
	}



	/**
	 * Renders HTML code for custom panel.
	 * @return string
	 */
	public function getPanel()
	{
		return '';
	}



	/**
	 * @param \Exception $e
	 *
	 * @return array
	 */
	public static function renderException($e)
	{
		
	}



	/**
	 * @return \static
	 */
	public static function register()
	{
		Debugger::$blueScreen->addPanel(array($panel = new static(), 'renderException'));
		Debugger::$bar->addPanel($panel);
		return $panel;
	}

}
