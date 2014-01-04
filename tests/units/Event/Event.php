<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Iridium\Components\EventDispatcher\tests\units\Event;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum ,
    \Iridium\Components\EventDispatcher\Event\Event as IrEvent;

/**
 * Description of Event
 *
 * @author Mathieu
 */
class Event extends atoum
{

    public function testCreate()
    {
        $test       = new IrEvent( 'test' );
        $dispatcher = new \mock\Iridium\Components\EventDispatcher\Dispatcher\Dispatcher();

        $this->object( $test )
                ->isInstanceOf( '\Iridium\Components\EventDispatcher\Event\Event' )
                ->string( $test->getName() )
                ->isEqualTo( 'test' )
                ->when( $test->setDispatcher( $dispatcher ) )
                ->object( $test->getDispatcher() )
                ->isInstanceOf( '\mock\Iridium\Components\EventDispatcher\Dispatcher\Dispatcher' );
    }

    public function testSetNameThrowsException()
    {
        // name is not a string
        $this->exception( function () {
                    $test = new IrEvent( 'test' );
                    $test->setName( null );
                } )
                ->isInstanceOf( '\InvalidArgumentException' )
                ->hasMessage( 'Event name must be a non empty string and must only contain letters, digits, hyphens and dots' )
                ->exception( function () {
                    $test = new IrEvent( 'test' );
                    $test->setName( 'test£' );
                } )
                ->isInstanceOf( '\InvalidArgumentException' )
                ->hasMessage( 'Event name must be a non empty string and must only contain letters, digits, hyphens and dots' );
    }

    public function testAttach()
    {
        $test = new IrEvent( 'test' );

        $this->array( $test->getAttachments() )
                ->isEmpty();

        $test = new IrEvent( 'test' , array( new \stdClass() ) );
        $this->array( $test->getAttachments() )
                ->isNotEmpty()
                ->object( $test->getIterator() )
                ->isInstanceOf( '\ArrayIterator' );
    }

}
