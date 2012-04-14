<?php
namespace FactoryGirl\Doctrine\Zend\Validate;

/**
 * @category   FactoryGirl
 * @package    Doctrine
 * @subpackage Zend
 * @author     Mikko Hirvonen <mikko.petteri.hirvonen@gmail.com>
 */
class RecordExistsValidator extends AbstractExistsValidator
{
    /**
     * Is valid
     *
     * @param  string
     * @return boolean
     */
    public function isValid($value)
    {
        $valid = true;
        $this->_setValue($value);

        $result = $this->_query($value);

        if (!$result) {
            $valid = false;
            $this->_error(self::ERROR_NO_RECORD_FOUND);
        }

        return $valid;
    }
}
