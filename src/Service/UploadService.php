<?php
/**
 * EPI License.
 */

namespace App\Service;

use App\Entity\Attachment;
use App\Entity\Bug;
use App\Repository\AttachmentRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service to upload files.
 */
class UploadService
{
    /**
     * @var AttachmentRepository repository with the files
     */
    private AttachmentRepository $attachRep;
    /**
     * @var ParameterBagInterface
     *                            get request parameters
     */
    private ParameterBagInterface $params;

    /**
     * @param AttachmentRepository  $attachRep rep
     * @param ParameterBagInterface $params    http parameters
     *
     *                                         Constructor
     */
    public function __construct(AttachmentRepository $attachRep, ParameterBagInterface $params)
    {
        $this->attachRep = $attachRep;
        $this->params = $params;
    }

    /**
     * @param     $files attachments
     * @param Bug $bug   the bug
     *
     * @return void
     *
     * Find bug by ID
     */
    public function saveFiles($files, Bug $bug): void
    {
        foreach ($files as $imageFile) {
            // Generate a unique filename
            $fileName = md5(uniqid()).'.'.$imageFile->guessExtension();

            // Move the file to the desired directory
            $imageFile->move(
                $this->params->get('upload_directory'),
                $fileName
            );

            $file = new Attachment();
            $file->setPath($fileName);
            $file->setOriginalName($imageFile->getClientOriginalName());
            $file->setBug($bug);
            $this->attachRep->save($file);
        }
    }
}
