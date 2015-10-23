<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 2:53 PM
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Websites;
use Doctrine\Common\DataFixtures\FixtureInterface;
use AppBundle\Entity\Users;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class FixtureLoader implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new Users();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setPhoneNumber(32141234);
        $user->setEmail('john@example.com');
        $user->setUsername('admin');
        $user->setSalt(md5(time()));

        $website = new Websites();
        $website->setStatus('enabled');
        $website->setName('Vk');
        $website->setUrl('http://vk.com');

        // шифрует и устанавливает пароль для пользователя,
        // эти настройки совпадают с конфигурационными файлами
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword('admin', $user->getSalt());
        $user->setPassword($password);
        $manager->persist($user);
        $manager->persist($website);
        $manager->flush();
    }
}