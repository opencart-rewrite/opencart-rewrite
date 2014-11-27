<?php
/**
 * Repository Settting.
 *
 * PHP version 5
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
namespace Entity;
use \Doctrine\ORM\EntityRepository;
use Entity\Setting;

/**
 * Repository to abstract operation on Setting
 *
 * @category Repository
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class SettingRepository extends EntityRepository
{
    /**
     * Get the list of settings of a shop belonging to same code
     * the returned array is associative {key: value}
     * value can be a string or an array itself
     *
     * @param string $code   name of the code of settings we want
     * @param string $storeId restrict to the setting of that shop
     *
     * @return array
     */
    public function getCodeSettings($code, $storeId = 0)
    {

        $settings = $this->createQueryBuilder('Entity\Setting')
            ->select('s')
            ->from('Entity\Setting', 's')
            ->where('s.storeid = :storeid')
            ->andWhere('s.code = :code')
            ->setParameter('id', (int) $storeId)
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult()
        ;

        $data = array();
        foreach ($settings as $oneSetting) {
            $data[$oneSetting->getKey()] = $oneSetting->getValue();
        }
        return $data;
    }

    /**
     *
     */
    public function editCodeSetting(
        $code,
        $data,
        $storeId = 0
    ) {
        $storeId = (int) $storeId;

        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $connection->beginTransaction();

        // remove config keys that does not start with $code
        $data = array_filter(
            $data,
            function ($key) use ($code) {
                return substr($key, 0, strlen($code)) == $code;
            },
            ARRAY_FILTER_USE_KEY
        );

        try {

            $this->deleteSettings($code, $storeId);


            foreach ($data as $key => $value) {
                $setting = new Setting();
                $setting->setKey($key);
                $setting->setCode($code);
                $setting->setStoreId($storeId);
                $setting->setValue($value);

                $em->persist($setting);
            }
            //TODO: not sure if needed as we have commit below
            $em->flush();

            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }
    }

    /**
     *
     */
    public function deleteSettings($code, $storeId = 0)
    {
        $dql = '
            DELETE
            FROM Entity\Setting s
            WHERE s.storeId = :storeId AND `code` = :code
        ';
        $this->createQuery($dql)
            ->setParameter('storeId', $storeId)
            ->setParameter('code', $code)
            ->execute()
        ;
    }

    /**
     *
     */
    public function editSettingValue(
        $code,
        $key,
        $value,
        $storeId = 0
    ) {
        $setting = $this->findOneBy(
            array(
                "code" => $code,
                "storeId" => (int) $storeId,
                "key" => $key
            )
        );
        $setting->setValue($value);

        $em = $this->getEntityManager();
        $em->persist($setting);
        $em->flush();
    }
}
