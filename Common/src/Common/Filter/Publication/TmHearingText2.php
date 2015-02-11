<?php

/**
 * Transport Manager Hearing text 1 filter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * TM Hearing text 2 filter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class TmHearingText2 extends Text1
{
    protected $tmHearingText2 = 'Article 6 of Regulation (EC) No 1071/2009';

    /**
     * @param \Common\Data\Object\Publication  $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $publication->offsetSet('text2', $this->tmHearingText2);

        return $publication;
    }
}
