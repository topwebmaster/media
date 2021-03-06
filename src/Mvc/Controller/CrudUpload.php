<?php
/**
 * This file is part of Vegas package.
 * 
 * Crud with additional upload action.
 * 
 * @author Arkadiusz Ostrycharz <arkadiusz.ostrycharz@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Mvc\Controller;

use Vegas\Mvc\Controller\Crud\Exception\UploaderNotSetException;

class CrudUpload extends Crud
{
    /**
     * @ACL(name="upload", inherit='edit')
     */
    public function uploadAction()
    {
        if (!$this->di->has('uploader')) {
            throw new UploaderNotSetException();
        }

        $this->view->disable();
        $this->dispatcher->getEventsManager()->fire(Crud\Events::BEFORE_UPLOAD, $this);

        $files = array();
        
        if($this->request->isAjax() && $this->request->hasFiles()) {
            try {
                $uploader = $this->di->get('uploader');
                $files[] = $uploader->setFiles($this->request->getUploadedFiles())->handle();
            } catch (\Exception $e) {
                $files[] = array('error' => $e->getMessage());
            }
        }
        
        $this->dispatcher->getEventsManager()->fire(Crud\Events::AFTER_UPLOAD, $this);
        return $this->response->setJsonContent(array('files' => $files));
    }  
}