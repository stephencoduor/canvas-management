<?php

require_once 'common.inc.php';

use smtech\StMarksSmarty\StMarksSmarty;
use Battis\BootstrapSmarty\NotificationMessage;

$toolbox->getSmarty()->enable(StMarksSmarty::MODULE_COLORPICKER);

define("STEP_INSTRUCTIONS", 1);
define("STEP_CONFIRM", 2);
define("STEP_SET_COLOR", 3);

$step = (empty($_REQUEST['step']) ? STEP_INSTRUCTIONS : $_REQUEST['step']);

switch ($step) {
    case STEP_SET_COLOR:
        if (empty($_REQUEST['course'])) {
            $toolbox->smarty_addMessage(
                'Course required',
                'Hard to set the color for an unspecified course.',
                NotificationMessage::ERROR
            );
        } else {
            if (empty($_REQUEST['color'])) {
                $toolbox->smarty_addMessage(
                    'Color required',
                    'Hard to set a color without the color',
                    NotificationMessage::ERROR
                );
            } else {
                $color = preg_replace('/#/', '', $_REQUEST['color']);
                try {
                    $enrollments = $toolbox->api_get("courses/{$_REQUEST['course']}/enrollments");
                    foreach ($enrollments as $enrollment) {
                        $toolbox->api_put(
                            "users/{$enrollment['user']['id']}/colors/course_{$_REQUEST['course']}",
                            array(
                                'hexcode' => $color
                            )
                        );
                    }
                    $toolbox->smarty_addMessage(
                        'Color updated',
                        "Updated the course color to <span style=\"color: #$color; background: white; border-radius: .25em; padding: .1em;\">#$color &#9724;</span> for <a target=\"_top\" href=\"" . $_SESSION[CANVAS_INSTANCE_URL] . '/courses/' . $_REQUEST['course'] . '/users">' . $enrollments->count() . ' users</a>.'
                    );
                } catch (Exception $e) {
                    $toolbox->exceptionErrorMessage($e);
                }
            }
        }

        /* TODO should really objectify rather than using gotos, huh? */
        /* flow into STEP_CONFIRM (and thence STEP_INSTRUCTIONS) */

    case STEP_CONFIRM:
        if ($step == STEP_CONFIRM) {
            if (empty($_REQUEST['course'])) {
                $toolbox->smarty_addMessage(
                    'Course required',
                    'Hard to set the color for an unspecified course.',
                    NotificationMessage::ERROR
                );
            } else {
                try {
                    $courses = $toolbox->api_get(
                        // FIXME don't hard code account numbers... yeesh
                        'accounts/1/courses',
                        array(
                            'search_term' => $_REQUEST['course'],
                            'include[]' => 'term'
                        )
                    );
                    $toolbox->smarty_assign('courses', $courses);
                    $toolbox->smarty_assign('formHidden', array('step' => STEP_SET_COLOR));
                    $toolbox->smarty_display(basename(__FILE__, '.php') . '/confirm.tpl');
                    exit;
                } catch (Exception $e) {
                    $toolbox->exceptionErrorMessage($e);
                }
            }
        }

        /* flow into STEP_INSTRUCTIONS */

    case STEP_INSTRUCTIONS:
    default:
        $toolbox->smarty_assign('formHidden', array('step' => STEP_CONFIRM));
        $toolbox->smarty_display(basename(__FILE__, '.php') . '/instructions.tpl');
}
