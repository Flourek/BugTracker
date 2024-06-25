<?php
/**
 * Task fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Attachment;
use App\Entity\Bug;
use App\Entity\User;
use App\Type\StatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 *  Fixtures class for the whole app, fills out the database with users, bugs, attachments.
 */
class AppFixtures extends Fixture
{
    /**
     * @var Generator
     *                Faker
     */
    protected Generator $faker;

    /**
     * @var ObjectManager
     *                    Persistence object manager
     */
    protected ObjectManager $manager;

    private PasswordHasherFactoryInterface $passwordHasherFactory;

    /**
     * @param PasswordHasherFactoryInterface $passwordHasherFactory
     *                                                              Constructor
     */
    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory)
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
    }

    /**
     * @param ObjectManager $manager
     *                               Load
     */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        $admin = new User();
        $admin->setUsername('admin');
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);
        $hashedPassword = $passwordHasher->hash('admin');
        $admin->setPassword($hashedPassword);
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        for ($i = 0; $i < 40; ++$i) {
            $user = new User();
            $user->setUsername($this->faker->userName());
            $user->setPassword('haslo');
            $manager->persist($user);

            $bug = new Bug();
            $bug->setTitle($this->faker->sentence(3));
            $bug->setBody($this->faker->sentence(200));
            $bug->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $bug->setAuthor($user);

            $envs = ['Linux', 'Windows'];
            $bug->setEnviroment($envs[array_rand($envs)]);
            $versions = ['1.3.01', '1.3.02', '1.3.03', '1.3.07', '0.9.4', '0.9.5'];
            $bug->setVersion($versions[array_rand($versions)]);
            $bug->setStatus(StatusEnum::intToKey(random_int(0, 2)));

            $files = ['1.jpg', '2.jpg', '3.jpg', '4.jpg', '5.jpg', '6.jpg', '7.jpg', '8.gif'];

            $randomIterations = random_int(0, 3); // Generate a random number of iterations

            for ($j = 0; $j < $randomIterations; ++$j) {
                // Perform your loop operations here
                echo 'Iteration: '.($i + 1)."\n";
                $file = new Attachment();
                $filename = $files[array_rand($files)];
                $file->setPath('fixtures/'.$filename);
                $file->setOriginalName($filename);
                $file->setBug($bug);
                $manager->persist($file);
            }
            $manager->persist($bug);
        }

        $manager->flush();
    }
}
