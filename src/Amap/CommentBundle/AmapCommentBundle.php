<?php

namespace Amap\CommentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AmapCommentBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSCommentBundle';
    }
}
