<?php


namespace Fredy\Exception;


class PageNotFoundException extends ControllerException
{
    protected $action = 'notFound';
}