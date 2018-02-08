<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;

/**
 * Class AbstractFilterableController
 */
abstract class AbstractFilterableController extends Controller
{
    /**
     * @return string
     */
    abstract public function getFilterForm();

    /**
     * @return string
     */
    abstract public function getFilterFormName();

    /**
     * @return string
     */
    abstract public function getRedirectUrl();

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function getFilterFormView()
    {
        return $this->createForm($this->getFilterForm(), $this->getFilters())->createView();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function filterAction(Request $request)
    {
        $form = $this->createForm($this->getFilterForm());

        if ($request->isMethod(Request::METHOD_POST) && $form->handleRequest($request)->isValid()) {
            $this->setFilters($this->getFilterFormName(), $form->getData());
        }

        /* handle knp sorting */
        $queryParams = $request->query->all();
        $url = $this->getRedirectUrl();

        if (count($queryParams) !== 0) {
            $url .= '?'.http_build_query($request->query->all());
        }

        return $this->redirect($url);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function resetAction(Request $request)
    {
        $this->setFilters($this->getFilterFormName(), array());

        /* handle knp sorting */
        $queryParams = $request->query->all();
        $url = $this->getRedirectUrl();

        if (count($queryParams) !== 0) {
            $url .= '?'.http_build_query($request->query->all());
        }

        return $this->redirect($url);
    }

    /**
     * @param $name
     * @param $value
     */
    protected function setFilters($name, $value)
    {
        foreach ($value as $key => $data) {
            if (is_object($data) && false !== strstr(get_class($data), 'Entity')) {
                $value[$key] = array('entity' => get_class($data), 'id' => $data->getId());
            }
        }

        $this->get('session')->set($name.'.filters', $value);
    }

    /**
     * @return mixed
     */
    protected function getFilters()
    {
        $value = $this->get('session')->get($this->getFilterFormName().'.filters', array());

        $entityManager = $this->getDoctrine()->getManager();
        foreach ($value as $key => $data) {
            if (is_array($data) && isset($data['entity']) && isset($data['id'])) {
                $value[$key] = $entityManager->getRepository($data['entity'])->find($data['id']);
            } elseif ($data instanceof Collection) {
                $value[$key] = [];
                foreach ($data as $entity) {
                    $value[$key][] = $entityManager->merge($entity);
                }
            }
        }

        return $value;
    }
}
