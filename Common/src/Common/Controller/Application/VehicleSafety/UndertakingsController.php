<?php

/**
 * Vehicle Undertakings Controller (OLCS-2855)
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */

namespace Common\Controller\Application\VehicleSafety;

/**
 * Vehicle Controller
 *
 * @author Jess Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class UndertakingsController extends VehicleSafetyController
{

    /**
     * Action data map
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'application',
                'smallVehiclesIntention',
                'nineOrMore',
                'limousinesNoveltyVehicles'
            )
        )
    );

    /**
     * Holds the licenceDataBundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'psvOperateSmallVhl',
            'psvSmallVhlNotes',
            'psvSmallVhlConfirmation',
            'psvNoSmallVhlConfirmation',
            'psvLimousines',
            'psvNoLimousineConfirmation',
            'psvOnlyLimousinesConfirmation',
        ),
        'children' => array(
            'trafficArea' => array(
                'properties' => array(
                    'id',
                    'isScottishRules',
                ),
            ),
            'status' => array(
                'properties' => array(
                    'id'
                )
            )
        )
    );

    /**
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    /**
     * Load the data for the form and format it in a way that the fieldsets can
     * understand.
     *
     * @param arary $data
     * @return array
     */
    protected function processLoad($data)
    {
        $translator = $this->getServiceLocator()->get('translator');

        $data['application'] = array(
            'id' => $data['id'],
            'version' => $data['version'],
            'status' => $data['status']['id']
        );

        // Load up the data in a format which can be understood by the fieldsets
        $data['smallVehiclesIntention'] = array(
            'psvOperateSmallVhl' => ($data['psvOperateSmallVhl']!=null?
                                                    $data['psvOperateSmallVhl']:false),
            'psvSmallVhlNotes' => ($data['psvSmallVhlNotes']!=null?
                                                    $data['psvSmallVhlNotes']:""),
            'psvSmallVhlUndertakings' =>
                $translator->translate(
                    'application_vehicle-safety_undertakings.smallVehiclesUndertakings.text'
                ),
            'psvSmallVhlScotland' =>
                $translator->translate(
                    'application_vehicle-safety_undertakings.smallVehiclesUndertakingsScotland.text'
                ),
            'psvSmallVhlConfirmation' => ($data['psvSmallVhlConfirmation']=='Y')
        );

        $data['nineOrMore'] = array(
            'psvNoSmallVhlConfirmation' => ($data['psvNoSmallVhlConfirmation']=='Y')
        );

        $data['limousinesNoveltyVehicles'] = array(
            'psvLimousines' => ($data['psvLimousines']?'Y':'N'),
            'psvNoLimousineConfirmation' => ($data['psvNoLimousineConfirmation']!=null?
                                                    $data['psvNoLimousineConfirmation']:false),
            'psvOnlyLimousinesConfirmation' => ($data['psvOnlyLimousinesConfirmation']!=null?
                                                    $data['psvOnlyLimousinesConfirmation']:false)
        );

        return $data;
    }

    /**
     * Save the form data
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data, $service = null)
    {
        if ( isset($data['psvSmallVhlConfirmation']) ) {
            $data['psvSmallVhlConfirmation']=($data['psvSmallVhlConfirmation']?'Y':'N');
        }

        if ( isset($data['psvNoSmallVhlConfirmation']) ) {
            $data['psvNoSmallVhlConfirmation']=($data['psvNoSmallVhlConfirmation']==1?'Y':'N');
        }

        if ( isset($data['psvLimousines']) ) {
            $data['psvLimousines']=($data['psvLimousines']=='Y');
        }

        parent::save($data, 'Application');
    }

    /**
     * Add customisation to the form dependent on which of five scenarios
     * is in play for OLCS-2855
     *
     * @param Form $form
     * @return Form
     */
    protected function alterForm($form)
    {
        $data = $this->load($this->getIdentifier());
        $options['data']=$data;
        $options['isReview']=false;

        $form=$this->makeFormAlterations($form, $this, $options);

        return $form;
    }

    /**
     * Make form alterations
     *
     * This method enables the summary to apply the same form alterations. In this
     * case we ensure we manipulate the form based on whether the license is PSV or not
     *
     * @param Form $form
     * @param mixed $context
     * @param array $options
     *
     * @return $form
     */
    public static function makeFormAlterations($form, $context, $options = array())
    {
        $data=$options['data'];

        // If this traffic area has no Scottish Rules flag, set it to false.
        if ( !isset($data['trafficArea']['isScottishRules']) ) {
            $data['trafficArea']['isScottishRules']=false;
        }

        // In some cases, totAuthSmallVhl etc. can be set NULL, and we
        // need to evaluate as zero, so fix that here.
        $arrayCheck=array('totAuthSmallVehicles','totAuthMediumVehicles','totAuthLargeVehicles');
        foreach ($arrayCheck as $attribute) {
            if ( is_null($data[$attribute]) ) {
                $data[$attribute]=0;
            }
        }

        // Need to enumerate the form fieldsets with their mapping, as we're
        // going to use old/new
        $fieldsetMap = Array();
        if ( $options['isReview'] ) {
            foreach ($options['fieldsets'] as $fieldset) {
                $fieldsetMap[$form->get($fieldset)->getAttribute('unmappedName')]=$fieldset;
            }
        } else {
            $fieldsetMap = Array(
                'smallVehiclesIntention' => 'smallVehiclesIntention',
                'limousinesNoveltyVehicles' => 'limousinesNoveltyVehicles',
                'nineOrMore' => 'nineOrMore'
            );
        }

        // Now remove the form fields we don't need to display to the user.
        if ( $data['totAuthSmallVehicles'] == 0 ) {
            // no smalls - case 3
            $form->remove($fieldsetMap['smallVehiclesIntention']);
        } else {
            // Small vehicles - cases 1, 2, 4, 5
            if ( ( $data['totAuthMediumVehicles'] == 0 )
                    && ( $data['totAuthLargeVehicles'] == 0 ) ) {
                // Small only, cases 1, 2
                if ( $data['trafficArea']['isScottishRules'] ) {
                    // Case 2 - Scottish small only
                    $form->get($fieldsetMap['smallVehiclesIntention'])->remove('psvOperateSmallVhl');
                    $form->get($fieldsetMap['smallVehiclesIntention'])->remove('psvSmallVhlNotes');
                    $form->remove($fieldsetMap['nineOrMore']);
                    $form->get($fieldsetMap['limousinesNoveltyVehicles'])->remove('psvOnlyLimousinesConfirmationLabel');
                    $form->get($fieldsetMap['limousinesNoveltyVehicles'])->remove('psvOnlyLimousinesConfirmation');
                } else {
                    // Case 1 - England/Wales small only
                    $form->remove($fieldsetMap['nineOrMore']);
                    $form->get($fieldsetMap['limousinesNoveltyVehicles'])->remove('psvOnlyLimousinesConfirmationLabel');
                    $form->get($fieldsetMap['limousinesNoveltyVehicles'])->remove('psvOnlyLimousinesConfirmation');
                }
            } else {
                // cases 4, 5
                if ( $data['trafficArea']['isScottishRules'] ) {
                    // Case 5 Mix Scotland
                    $form->get($fieldsetMap['smallVehiclesIntention'])->remove('psvOperateSmallVhl');
                    $form->get($fieldsetMap['smallVehiclesIntention'])->remove('psvSmallVhlNotes');
                    $form->remove($fieldsetMap['nineOrMore']);
                } else {
                    // Case 4 Mix England/Wales
                    $form->remove($fieldsetMap['nineOrMore']);
                }

            }
        }

        return $form;
    }
}
