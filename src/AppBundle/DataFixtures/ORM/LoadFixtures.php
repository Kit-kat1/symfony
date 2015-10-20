<?php
/**
 * Created by PhpStorm.
 * User: gunko
 * Date: 10/13/15
 * Time: 2:53 PM
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use AppBundle\Entity\Users;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Roles;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class FixtureLoader implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $role = new Roles();
        $role->setName('ADMIN_ROLE');
        $user = new Users();
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setPhoneNumber(32141234);
        $user->setEmail('john@example.com');
        $user->setUsername('admin');
        $user->setSalt(md5(time()));
        $user->setRoleId($role);

        // шифрует и устанавливает пароль для пользователя,
        // эти настройки совпадают с конфигурационными файлами
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
        $password = $encoder->encodePassword('admin', $user->getSalt());
        $user->setPassword($password);
        $manager->persist($role);
        $manager->persist($user);
        $manager->flush();
    }
}