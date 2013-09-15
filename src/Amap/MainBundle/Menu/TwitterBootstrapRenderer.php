<?php
 
namespace Amap\MainBundle\Menu;
 
use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\TwigRenderer;
 
class TwitterBootstrapRenderer extends TwigRenderer
{
	public function render(ItemInterface $item, array $options = array())
	{
		$options = array_merge(
			array('currentClass' => 'active'),
			$options
		);
		 
		if ('root' === $item->getName()) {
			$item->setAttribute('class', trim('nav '.$item->getAttribute('class')));
		}
		 
		return parent::render($item, $options);
	}
}