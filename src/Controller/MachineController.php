<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 */

namespace App\Controller;

use App\Bean\Factory\InformationFactory;
use App\Entity\Ip;
use App\Form\Type\MachineType;
use App\Entity\Machine;
use App\Manager\IpManager;
use App\Manager\MachineManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MachineController class.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 * @Route("machine")
 */
class MachineController extends Controller
{
    /**
     * Limit of machine per page for listing.
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * Lists all machine entities.
     *
     * @Route("/", name="default_machine_index")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_MACHINE')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //Retrieving all services
        $machineManager = $this->get(MachineManager::class);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $machineManager->getQueryBuilder(), /* queryBuilder NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT_PER_PAGE,
            ['defaultSortFieldName' => 'machine.label', 'defaultSortDirection' => 'asc']
        );

        return $this->render('@App/default/machine/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new machine entity.
     *
     * @Route("/new", name="default_machine_new")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_MACHINE')")
     *
     * @param Request $request
     *
     * @return RedirectResponse |Response
     */
    public function newAction(Request $request)
    {
        $machine = new Machine();
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $machineService = $this->get(MachineManager::class);
            $machineService->save($machine, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.machine.created %name%', ['%name%' => $machine->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_machine_show', array('id' => $machine->getId()));
        }

        return $this->render('@App/default/machine/new.html.twig', [
            'machine' => $machine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a machine entity.
     *
     * @Route("/{id}", name="default_machine_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_MACHINE')")
     *
     * @param Machine $machine
     *
     * @return Response
     */
    public function showAction(Machine $machine)
    {
        /** @var MachineManager $machineManager */
        $machineManager = $this->get(MachineManager::class);
        $deleteForm = $this->createDeleteForm($machine);
        $information = InformationFactory::createInformation($machine);
        $logs = $machineManager->retrieveLogs($machine);

        return $this->render('@App/default/machine/show.html.twig', [
            'isDeletable' => $machineManager->isDeletable($machine),
            'logs' => $logs,
            'information' => $information,
            'machine' => $machine,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing machine entity.
     *
     * @Route("/{id}/edit", name="default_machine_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_MACHINE')")
     *
     * @param Request $request The request
     * @param Machine $machine The machine entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Machine $machine)
    {
        $machineService = $this->get(MachineManager::class);
        $deleteForm = $this->createDeleteForm($machine);
        $editForm = $this->createForm(MachineType::class, $machine);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $machineService->save($machine, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.machine.updated %name%', ['%name%' => $machine->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_machine_show', array('id' => $machine->getId()));
        }
        $logs = $machineService->retrieveLogs($machine);
        $information = InformationFactory::createInformation($machine);

        return $this->render('@App/default/machine/edit.html.twig', [
            'isDeletable' => $machineService->isDeletable($machine),
            'logs' => $logs,
            'information' => $information,
            'machine' => $machine,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a machine entity.
     *
     * @Route("/{id}", name="default_machine_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_MANAGE_MACHINE')")
     *
     * @param Request $request The request
     * @param Machine $machine The $machine entity
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Machine $machine)
    {
        $form = $this->createDeleteForm($machine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $machineManager = $this->get(MachineManager::class);
            $machineManager->delete($machine);
            //Flash message.
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.machine.deleted %name%', ['%name%' => $machine->getLabel()]);
            $session->getFlashBag()->add('success', $message);
        }

        return $this->redirectToRoute('default_machine_index');
    }

    /**
     * Deletes an ip from network.
     *
     * @Route("/{id}/delete-ip", name="default_machine_delete_ip")
     * @ParamConverter("ip", class="App:Ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip      $ip      the ip to delete
     *
     * @return RedirectResponse|Response
     */
    public function deleteIpAction(Request $request, Ip $ip)
    {
        $trans = $this->get('translator.default');

        $form = $this->createDeleteIpForm($ip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Prepare the message before deleteing.
            $message = $trans->trans('default.ip.deleted %name%', [
                '%name%' => long2ip($ip->getIp()),
            ]);

            //Deleting
            $ipManager = $this->get(IpManager::class);
            $ipManager->delete($ip);

            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('success', $message);

            //Redirecting
            return $this->redirectToRoute('default_machine_show', ['id' => $ip->getMachine()->getId()]);
        } else {
            return $this->render('@App/default/machine/delete-ip.html.twig', [
                'confirm_form' => $form->createView(),
                'ip' => $ip,
            ]);
        }
    }

    /**
     * Unlink an IP from its machine.
     *
     * @Route("/{id}/unlink", name="default_machine_unlink")
     * @ParamConverter("ip", class="App:Ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip      $ip      The IP entity
     *
     * @return RedirectResponse | Response
     */
    public function unlinkAction(Request $request, Ip $ip)
    {
        $trans = $this->get('translator.default');

        if (null === $ip->getMachine()) {
            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('warning', $trans->trans('default.ip.unlink.error %ip%', [
                '%ip%' => ip2long($ip->getIp()),
            ]));

            //Redirecting
            return $this->redirectToRoute('default_machine_show', ['id' => $ip->getNetwork()->getId()]);
        }

        $form = $this->createUnlinkForm($ip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Prepare the message before unlinking.
            $message = $trans->trans('default.ip.unlink %ip% %name%', [
                '%ip%' => ip2long($ip->getIp()),
                '%name%' => $ip->getMachine()->getLabel(),
            ]);
            $idMachine = $ip->getMachine()->getId();

            //Unlinking
            $ipManager = $this->get(IpManager::class);
            $ipManager->unlink($ip);

            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('success', $message);

            //Redirecting
            return $this->redirectToRoute('default_machine_show', ['id' => $idMachine]);
        } else {
            return $this->render('@App/default/machine/unlink.html.twig', [
                'confirm_form' => $form->createView(),
                'ip' => $ip,
            ]);
        }
    }

    /**
     * Creates a form to delete a machine entity.
     *
     * @param Machine $machine The machine entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Machine $machine)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_machine_delete', array('id' => $machine->getId())))
            ->setMethod('DELETE')
            ->add('delete', SubmitType::class, [
                'attr' => ['class' => 'fa-js-trash-o btn-danger confirm-delete'],
                'label' => 'administration.delete.confirm.delete',
            ])
            ->getForm()
            ;
    }

    /**
     * Creates a form to ask confirmation before deleting an ip.
     *
     * @param Ip $ip the Ip entity
     *
     * @return Form The form
     */
    private function createDeleteIpForm(Ip $ip): Form
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_machine_delete_ip', array('id' => $ip->getId())))
            ->setMethod('DELETE')
            ->add('confirm', SubmitType::class, [
                'attr' => ['class' => 'btn-danger confirm-delete'],
                'icon' => 'trash-o',
                'label' => 'form.ip.delete-ip.confirm',
            ])
            ->getForm()
            ;
    }

    /**
     * Creates a form to dissociate/unlink an ip from a machine.
     *
     * @param Ip $ip the Ip entity
     *
     * @return Form The form
     */
    private function createUnlinkForm(Ip $ip): Form
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_machine_unlink', array('id' => $ip->getId())))
            ->setMethod('DELETE')
            ->add('confirm', SubmitType::class, [
                'attr' => ['class' => 'btn-danger confirm-delete'],
                'icon' => 'hand-spock-o ',
                'label' => 'form.ip.unlink.confirm',
            ])
            ->getForm()
            ;
    }
}
