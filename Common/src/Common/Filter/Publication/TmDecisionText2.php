<?php

/**
 * Transport Manager Decision text 2 filter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * TM Decision text 2 filter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class TmDecisionText2 extends Text1
{
    protected $tmText2 = 'Repute Not Lost under Article 6 of Regulation (EC) No 1071/2009';

    /**
     * @param \Common\Data\Object\Publication  $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $publication->offsetSet('text2', $this->tmText2);

        return $publication;
    }
}
