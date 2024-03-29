<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Events tests.
 *
 * @package    bookhrvoje_printy
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

/**
 * Events tests class.
 *
 * @package    bookhrvoje_printy
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bookhrvoje_printy_events_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_book_printed() {
        // There is no proper API to call to test the event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($book->cmid),
            'objectid' => $book->id
        );
        $event = \bookhrvoje_printy\event\book_printed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\bookhrvoje_printy\event\book_printed', $event);
        $this->assertEquals(context_module::instance($book->cmid), $event->get_context());
        $this->assertEquals($book->id, $event->objectid);
        $expected = array($course->id, 'book',  'printy', 'hrvoje/printy/index.php?id=' . $book->cmid, $book->id, $book->cmid);
        $this->assertEventLegacyLogData($expected, $event);
    }


    public function test_chapter_printed() {
        // There is no proper API to call to test the event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $chapter = $bookgenerator->create_chapter(array('bookid' => $book->id));

        $params = array(
            'context' => context_module::instance($book->cmid),
            'objectid' => $chapter->id
        );
        $event = \bookhrvoje_printy\event\chapter_printed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\bookhrvoje_printy\event\chapter_printed', $event);
        $this->assertEquals(context_module::instance($book->cmid), $event->get_context());
        $this->assertEquals($chapter->id, $event->objectid);
        $expected = array($course->id, 'book', 'print chapter', 'hrvoje/printy/index.php?id=' . $book->cmid .
            '&chapterid=' . $chapter->id, $chapter->id, $book->cmid);
        $this->assertEventLegacyLogData($expected, $event);
    }

}
