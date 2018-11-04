<?php

namespace App\Controllers\Ajax;

use App\Models\TagModel;
use Core\AjaxController;
use Core\JsonException;

class Tag extends AjaxController
{


    /**
     * Update the tag via ajax
     * @throws \Core\JsonException
     */
    public function update()
    {
        //security checks
        $this->onlyAdmin();
        if (!$this->request->isPost()) {
            throw new JsonException('Call is not post');
        }

        //prepating our return results
        $result = array();
        $tagUpdateJson = ($this->request->getData('tag-update'));
        $tagUpdate = json_decode($tagUpdateJson);

        //Converting our array of objects to simple array
        $send = array();
        foreach ($tagUpdate as $item) {
            $send[$item->name] = $item->value;
        }

        $tagModel = new TagModel($this->container);
        $result['success'] = $tagModel->update($send["idtags"], $send["tag_name"]);
        echo json_encode($result);
    }

    /**
     * Delete a tag via Ajax
     * @throws \Core\JsonException
     */
    public function delete()
    {
        //security checks
        $this->onlyAdmin();
        if (!$this->request->isPost()) {
            throw new JsonException('Call is not post');
        }

        //prepating our return results
        $result = array();
        $DeleteJson = ($this->request->getData('tag-delete'));
        $Delete = json_decode($DeleteJson);

        //Converting our array of objects to simple array
        $send = array();
        foreach ($Delete as $item) {
            $send[$item->name] = $item->value;
        }

        $tagModel = new TagModel($this->container);
        $result['success'] = $tagModel->delete($send["idtags"]);
        echo json_encode($result);
    }
}