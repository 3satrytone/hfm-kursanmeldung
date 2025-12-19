<?php
namespace Justorange\JoKursanmeldung\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Steffen Schneider <info@justorange.de>, JUSTORANGE
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Ensemble
 *
 */
class Ensemble extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * enconf
     *
     * @var integer
     */
    protected $enconf;

    /**
     * enname
     *
     * @var string
     */
    protected $enname;

    /**
     * entn
     *
     * @var integer
     */
    protected $entn;

    /**
     * enfirstn
     *
     * @var string
     */
    protected $enfirstn;

    /**
     * enlastn
     *
     * @var string
     */
    protected $enlastn;

    /**
     * eninstru
     *
     * @var string
     */
    protected $eninstru;

    /**
     * engebdate
     *
     * @var string
     */
    protected $engebdate;

    /**
     * ennatio
     *
     * @var string
     */
    protected $ennatio;

    /**
     * engrdate
     *
     * @var string
     */
    protected $engrdate;
    /**
     * entype
     *
     * @var string
     */
    protected $entype;
    /**
     * engrplace
     *
     * @var string
     */
    protected $engrplace;

    /**
     * Returns the enconf
     *
     * @return string $enconf
     */
    public function getEnconf()
    {
        return $this->enconf;
    }

    /**
     * Sets the enconf
     *
     * @param string $enconf
     * @return void
     */
    public function setEnconf($enconf)
    {
        $this->enconf = $enconf;
    }

    /**
     * Returns the enname
     *
     * @return string $enname
     */
    public function getEnname()
    {
        return $this->enname;
    }

    /**
     * Sets the enname
     *
     * @param string $enname
     * @return void
     */
    public function setEnname($enname)
    {
        $this->enname = $enname;
    }

    /**
     * Returns the entn
     *
     * @return integer $entn
     */
    public function getEntn()
    {
        return $this->entn;
    }

    /**
     * Sets the entn
     *
     * @param integer $entn
     * @return void
     */
    public function setEntn($entn)
    {
        $this->entn = $entn;
    }

    /**
     * Returns the enfirstn
     *
     * @return string $enfirstn
     */
    public function getEnfirstn()
    {
        return $this->enfirstn;
    }

    /**
     * Sets the enfirstn
     *
     * @param string $enfirstn
     * @return void
     */
    public function setEnfirstn($enfirstn)
    {
        $this->enfirstn = $enfirstn;
    }

    /**
     * Returns the enlastn
     *
     * @return string $enlastn
     */
    public function getEnlastn()
    {
        return $this->enlastn;
    }

    /**
     * Sets the enlastn
     *
     * @param string $enlastn
     * @return void
     */
    public function setEnlastn($enlastn)
    {
        $this->enlastn = $enlastn;
    }

    /**
     * Returns the eninstru
     *
     * @return string $eninstru
     */
    public function getEninstru()
    {
        return $this->eninstru;
    }

    /**
     * Sets the eninstru
     *
     * @param string $eninstru
     * @return void
     */
    public function setEninstru($eninstru)
    {
        $this->eninstru = $eninstru;
    }

    /**
     * Returns the engebdate
     *
     * @return string $engebdate
     */
    public function getEngebdate()
    {
        return $this->engebdate;
    }

    /**
     * Sets the engebdate
     *
     * @param string $engebdate
     * @return void
     */
    public function setEngebdate($engebdate)
    {
        $this->engebdate = $engebdate;
    }

    /**
     * Returns the ennatio
     *
     * @return string $ennatio
     */
    public function getEnnatio()
    {
        return $this->ennatio;
    }

    /**
     * Sets the ennatio
     *
     * @param string $ennatio
     * @return void
     */
    public function setEnnatio($ennatio)
    {
        $this->ennatio = $ennatio;
    }

    /**
     * Returns the engrdate
     *
     * @return string $engrdate
     */
    public function getEngrdate()
    {
        return $this->engrdate;
    }

    /**
     * Sets the engrdate
     *
     * @param string $engrdate
     * @return void
     */
    public function setEngrdate($engrdate)
    {
        $this->engrdate = $engrdate;
    }

    /**
     * Returns the entype
     *
     * @return string $entype
     */
    public function getEntype()
    {
        return $this->entype;
    }

    /**
     * Sets the entype
     *
     * @param string $entype
     * @return void
     */
    public function setEntype($entype)
    {
        $this->entype = $entype;
    }

    /**
     * Returns the engrplace
     *
     * @return string $engrplace
     */
    public function getEngrplace()
    {
        return $this->engrplace;
    }

    /**
     * Sets the engrplace
     *
     * @param string $engrplace
     * @return void
     */
    public function setEngrplace($engrplace)
    {
        $this->engrplace = $engrplace;
    }
}
