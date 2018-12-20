<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Classroom;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ClassroomType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ClassroomController extends Controller
{

    /**
     * @Route("/classroom/", name="classroom", methods={"GET"})
     */
    public function index()
    {
        $classrooms = $this->getDoctrine()->getRepository(Classroom::class)->findAll();
        return $this->render('classroom/index.html.twig', ['classrooms'=>$classrooms]);
    }

    /**
     * @Route("/classroom/create", name="classroom_create", methods={"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function create(Request $request)
    {
        $classroom = new Classroom();
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $classroom = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($classroom);
            $entityManager->flush();
            return $this->redirectToRoute('classroom');
        }
        return $this->render('classroom/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/classroom/edit/{id}", name="classroom_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function edit(Classroom $classroom, Request $request)
    {
        $form = $this->createForm(ClassroomType::class, $classroom);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $classroom = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($classroom);
            $entityManager->flush();
            return $this->redirectToRoute('classroom');
        }
        return $this->render('classroom/edit.html.twig', ['form' => $form->createView()]);
    }
    /**
     * @Route("/classroom/delete/{id}", name="classroom_delete", methods={"GET"})
     * @Security("has_role('ROLE_USER')")
     */
    public function delete($id)
    {
        $classroom = $this->getDoctrine()->getRepository(Classroom::class)->find($id);
        $students = $classroom->getStudents();
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($students as $student)
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
        }
//        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($classroom);
        $entityManager->flush();
        return $this->redirectToRoute('classroom');
    }


}
