<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\Controller;

use Everlution\AjaxcomBundle\DataObject\Callback;
use Everlution\AjaxcomBundle\Mutation;
use Everlution\AjaxcomBundle\Service\Ajaxcom;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

trait AjaxcomSymfony4Trait
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var Ajaxcom
     */
    private $ajaxcom;
    /**
     * @var Mutation\AddBlocks
     */
    private $ajaxcomAddBlocks;
    /**
     * @var Mutation\RemoveBlocks
     */
    private $ajaxcomRemoveBlocks;
    /**
     * @var Mutation\Callbacks
     */
    private $ajaxcomCallbacks;
    /**
     * @var Mutation\ReplaceClass
     */
    private $ajaxcomReplaceClass;
    /**
     * @var Mutation\ChangeUrl
     */
    private $ajaxcomChangeUrl;

    /** @required */
    public function setAjaxcomRequiredServices(
        RequestStack $requestStack,
        Ajaxcom $ajaxcom,
        Mutation\AddBlocks $ajaxcomAddBlocks,
        Mutation\RemoveBlocks $ajaxcomRemoveBlocks,
        Mutation\Callbacks $ajaxcomCallbacks,
        Mutation\ReplaceClass $ajaxcomReplaceClass,
        Mutation\ChangeUrl $ajaxcomChangeUrl
    ): void {
        $this->requestStack = $requestStack;
        $this->ajaxcom = $ajaxcom;
        $this->ajaxcomAddBlocks = $ajaxcomAddBlocks;
        $this->ajaxcomRemoveBlocks = $ajaxcomRemoveBlocks;
        $this->ajaxcomCallbacks = $ajaxcomCallbacks;
        $this->ajaxcomReplaceClass = $ajaxcomReplaceClass;
        $this->ajaxcomChangeUrl = $ajaxcomChangeUrl;
    }

    protected function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        $request = $this->requestStack->getMasterRequest();

        if ($request->server->get(Ajaxcom::AJAX_COM_HEADER, false)) {
            return $this->ajaxcom->handle($view, $parameters);
        }

        return parent::render($view, $parameters, $response);
    }

    protected function renderAjaxBlock(string $id): self
    {
        $this->ajaxcomAddBlocks->add($id);

        return $this;
    }

    protected function refreshAjaxBlock(string $id): self
    {
        $this->ajaxcomAddBlocks->refresh($id);

        return $this;
    }

    protected function removeAjaxBlock(string $selector): self
    {
        $this->ajaxcomRemoveBlocks->add($selector);

        return $this;
    }

    protected function addCallback(string $functionName, array $parameters = []): self
    {
        $this->ajaxcomCallbacks->add(new Callback($functionName, $parameters));

        return $this;
    }

    protected function replaceClass(string $selector, string $class): self
    {
        $this->ajaxcomReplaceClass->add($selector, $class);

        return $this;
    }

    protected function doNotChangeUrl(): self
    {
        $this->ajaxcomChangeUrl->doNotChangeUrl();

        return $this;
    }
}