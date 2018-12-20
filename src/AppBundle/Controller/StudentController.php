<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Student;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\StudentType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class StudentController extends Controller
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function homepage()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/student/", name="student", methods={"GET"})
     */
    public function index()
    {
        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
        return $this->render('student/index.html.twig', ['students'=>$students]);
    }

    /**
     * @Route("/student/create", name="student_create", methods={"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function create(Request $request)
    {

        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $student = $form->getData();
            $file = $student->getImage();
            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            try {
                $file->move($this->getParameter('images_dir'),$fileName);
            } catch (FileException $e) {

            }
            $student->setImage($fileName);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();
            return $this->redirectToRoute('student');
        }
        return $this->render('student/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/student/edit/{id}", name="student_edit", methods={"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function edit(Student $student, Request $request)
    {
        $img= $student->getImage();
        if($img !== null) {
            $student->setImage(new File($this->getParameter('images_dir').'/'.$img));
        }
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($student->getImage() !== null) {
                $newImage= $student->getImage();
                $newImageName= $this->generateUniqueFileName().$newImage->guessExtension();
                $newImage->move($this->getParameter('images_dir'), $newImageName);
                $student->setImage($newImageName);
            } else {
                $student->setImage($img);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();
            return $this->redirectToRoute('student');
        }
        return $this->render('student/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/student/delete/{id}", name="student_delete", methods={"GET"})
     * @Security("has_role('ROLE_USER')")
     */
    public function delete(Student $student)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($student);
        $entityManager->flush();
        return $this->redirectToRoute('student');
    }

    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

}
