<?php
/*
 * space-tactics-php8
 * PlanetRepository.php | 1/27/23, 10:49 PM
 * Copyright (C)  2023 ShaoKhan
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Repository;

use App\Entity\Planet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Planet>
 *
 * @method Planet|null find($id, $lockMode = NULL, $lockVersion = NULL)
 * @method Planet|null findOneBy(array $criteria, array $orderBy = NULL)
 * @method Planet[]    findAll()
 * @method Planet[]    findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
 */
class PlanetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $planet)
    {
        parent::__construct($planet, Planet::class);
    }

    public function save(Planet $entity, bool $flush = FALSE): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Planet $entity, bool $flush = FALSE): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Planet[] Returns an array of Planet objects
     */
    public function findByField($value): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user_uuid = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            #->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findFirst($uuid): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user_uuid = :val')
            ->setParameter('val', $uuid)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $userid
     * @param string $slug
     *
     * @return array Returns one Planet by Player and PlanetSlug
     */
    public function findByPlayerIdAndSlug(string $userid, string $slug): array
    {

        return $this->createQueryBuilder('p')
            ->andWhere('p.user_uuid = :val')
            ->andWhere('p.slug = :slug')
            ->setParameter('val', $userid)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param                 $uuid
     * @param                 $slug
     * @param ManagerRegistry $mr
     *
     * @return array Buildings
     */
    public function getPlanetBuildings($uuid, $slug, $mr): array
    {

        $br = new BuildingsRepository($mr);
        $brqb = ($br->createQueryBuilder('b'));
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
            p.metal_building, 
            p.crystal_building,
            p.deuterium_building,
            p.solar_building,
            p.nuclear_building,
            p.robot_building,
            p.nanite_building,
            p.hangar_building,
            p.metalstorage_building,
            p.crystalstorage_building,
            p.deuteriumstorage_building,
            p.laboratory_building,
            p.university_building,
            p.alliancehangar_building,
            p.missilesilo_building
            ',
            )
            ->andWhere('p.user_uuid = :val')
            ->andWhere('p.slug = :slug')
            ->setParameter('val', $uuid)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getPlanetNamesByUuid(string $uuid)
    {

        return $this->createQueryBuilder('p')
            #->select('p.name as name, p.slug as slug, p.system_x, p.system_y, p-system_z')
            ->andWhere('p.user_uuid = :val')
            ->setParameter('val', $uuid)
            ->getQuery()
            ->getResult();

    }

    public function getPlanetScience($uuid, $slug, ManagerRegistry $mr): array
    {

        $sr = new ScienceRepository($mr);
        $sr->createQueryBuilder('b');
        $qb = $this->createQueryBuilder('p')
            ->select(
                '
            p.metal_building, 
            p.crystal_building,
            p.deuterium_building,
            p.solar_building,
            p.nuclear_building,
            p.robot_building,
            p.nanite_building,
            p.hangar_building,
            p.metalstorage_building,
            p.crystalstorage_building,
            p.deuteriumstorage_building,
            p.laboratory_building,
            p.university_building,
            p.alliancehangar_building,
            p.missilesilo_building
            ',
            )
            ->andWhere('p.user_uuid = :val')
            ->andWhere('p.slug = :slug')
            ->setParameter('val', $uuid)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();

        return $qb;
    }

    public function getDarkmatter($uuid)
    {
        return $this->createQueryBuilder('p')
            ->select('p.darkmatter')
            ->where('p.user_uuid = :uuid')
            ->andWhere('p.darkmatter IS NOT null')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getResult();
    }

    public function getAllCoords()
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.system_x x, p.system_y y, p.system_z z')
            ->getQuery()
            ->getResult();
        foreach ($query as $key => $value) {
            $coords[] = ['x' => $value['x'], 'y' => $value['y'], 'z' => $value['z']];
        }
        return $coords;
    }

    public function getLevelByName($building, $planeteSlug)
    {
        return $this->createQueryBuilder('p')
            ->select('p.' . $building . ' as level')
            ->where('p.slug = :slug')
            #->andWhere('p.user_uuid = :uuid')
            ->setParameter('slug', $planeteSlug)
            #->setParameter('uuid', $_SESSION['uuid'])
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Planet
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
