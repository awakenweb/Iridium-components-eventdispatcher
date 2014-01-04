<?php

namespace Iridium\Components\EventDispatcher\tests\units\Dispatcher;

require_once __DIR__ . '/../../../vendor/autoload.php';

use \atoum ,
    \Iridium\Components\EventDispatcher\Dispatcher\Dispatcher as IrEvDisp;

class TestClass
{

    public function handleTest($event)
    {
        foreach ($event as $message) {
            echo $message;
        }
    }

    public function anotherHandleTest($event)
    {
        foreach ($event as $message) {
            echo $message;
        }
    }

}

class Dispatcher extends atoum
{

    public function testCreateDispatcher()
    {
        $eventDispatcher = new IrEvDisp();
        $this->object( $eventDispatcher )
                ->isInstanceOf( '\Iridium\Components\EventDispatcher\Dispatcher\Dispatcher' );
    }

    public function testAddHandler()
    {
        $eventDispatcher = new IrEvDisp();

        $handler = new \mock\Iridium\Components\EventDispatcher\tests\units\Dispatcher\TestClass();

        $this->object( $eventDispatcher->subscribeToEvent( 'test.this-is-a-test' , array( $handler , 'handleTest' ) ) )
                ->isInstanceOf( '\Iridium\Components\EventDispatcher\Dispatcher\Dispatcher' );
    }

    public function testAddHandlerThrowsExceptions()
    {
        $eventDispatcher = new IrEvDisp();

        $handler = new \mock\Iridium\Components\EventDispatcher\tests\units\Dispatcher\TestClass();

        // event name not a string
        $this->exception( function () use ($eventDispatcher , $handler) {
                    $eventDispatcher->subscribeToEvent( new \stdClass() , array( $handler , 'handleTest' ) );
                } )
                ->isInstanceOf( '\InvalidArgumentException' )
                ->hasMessage( 'Event name must be a string, object given' );

        // event name illegal char
        $this->exception( function () use ($eventDispatcher , $handler) {
                    $eventDispatcher->subscribeToEvent( '£' , array( $handler , 'handleTest' ) );
                } )
                ->isInstanceOf( '\InvalidArgumentException' )
                ->hasMessage( 'Event name does not respect naming convention' );

        $this->exception( function () use ($eventDispatcher , $handler) {
                    $eventDispatcher->subscribeToEvent( 'test.this-is-a-test' , 'test' );
                } )
                ->isInstanceOf( '\InvalidArgumentException' )
                ->hasMessage( "Handler for 'test.this-is-a-test' must be a valid callable callback" );

        $this->exception( function () use ($eventDispatcher , $handler) {
                    $eventDispatcher->subscribeToEvent( 'test.this-is-a-test' , array( $handler , 'handleTest' ) , new \StdClass() );
                } )
                ->isInstanceOf( '\InvalidArgumentException' )
                ->hasMessage( 'Priority must be an integer, object given' );

        $this->exception( function () use ($eventDispatcher , $handler) {
                    $eventDispatcher->subscribeToEvent( 'test.this-is-a-test' , array( $handler , 'handleTest' ) , 1 );
                    $eventDispatcher->subscribeToEvent( 'test.this-is-a-test' , array( $handler , 'handleTest' ) , 1 );
                } )
                ->isInstanceOf( '\UnexpectedValueException' )
                ->hasMessage( "An event handler is already defined at priority number '1'" );
    }

    public function testDispatchOneEventInOneHandler()
    {
        $eventDispatcher = new IrEvDisp();
        $handler         = new \mock\Iridium\Components\EventDispatcher\tests\units\Dispatcher\TestClass();

        $eventDispatcher->subscribeToEvent( 'test.test' , array( $handler , 'handleTest' ) );

        $event = new \mock\Iridium\Components\EventDispatcher\Event\Event( 'test.test' , array( 'bonjour' ) );
        $this->output( function () use ($eventDispatcher , $event) {
                    $eventDispatcher->dispatch( $event );
                }
                )->isEqualTo( 'bonjour' )
                ->mock( $event )
                ->call( 'getIterator' )
                ->once();
    }

    public function testDispatchTwoEventsInOneHandler()
    {
        $eventDispatcher = new IrEvDisp();
        $handler         = new \mock\Iridium\Components\EventDispatcher\tests\units\Dispatcher\TestClass();

        $eventDispatcher->subscribeToEvent( 'test.test' , array( $handler , 'handleTest' ) );

        $event  = new \mock\Iridium\Components\EventDispatcher\Event\Event( 'test.test' , array( 'bonjour' ) );
        $event2 = new \mock\Iridium\Components\EventDispatcher\Event\Event( 'test.test2' , array( 'bonjour' ) );
        $this->output( function () use ($eventDispatcher , $event , $event2) {
                    $eventDispatcher->dispatch( $event );
                    $eventDispatcher->dispatch( $event2 );
                }
                )->isEqualTo( 'bonjour' )
                ->mock( $event )
                ->call( 'getIterator' )
                ->once()
                ->mock( $handler )
                ->call( 'handleTest' )
                ->once();
    }

    public function testDispatchOneEventInTwoHandlers()
    {
        $eventDispatcher = new IrEvDisp();
        $handler         = new \mock\Iridium\Components\EventDispatcher\tests\units\Dispatcher\TestClass();

        $eventDispatcher
                ->subscribeToEvent( 'test.test' , array( $handler , 'handleTest' ) , 1 )
                ->subscribeToEvent( 'test.test' , array( $handler , 'anotherHandleTest' ) , 11 );

        $event = new \mock\Iridium\Components\EventDispatcher\Event\Event( 'test.test' , array( 'bonjour' ) );
        $this->output( function () use ($eventDispatcher , $event) {
                    $eventDispatcher->dispatch( $event );
                }
                )->isEqualTo( 'bonjourbonjour' )
                ->mock( $event )
                ->call( 'getIterator' )
                ->atLeastOnce()
                ->mock( $handler )
                ->call( 'handleTest' )
                ->once()
                ->call( 'anotherHandleTest' )
                ->once();
    }

    public function testDispatchThrowsException()
    {
        $eventDispatcher = new IrEvDisp();

        $this->exception( function () use ($eventDispatcher) {
                    $eventDispatcher->dispatch( new \stdClass() );
                } )
                ->isInstanceOf( '\InvalidArgumentException' )
                ->hasMessage( "Event must be a string or a EventDispatcher Event object. stdClass given" );
    }

    public function testDispatchCreateObjectWhenStringGiven()
    {
        $eventDispatcher = new IrEvDisp();

        $this->object($eventDispatcher->dispatch('test.test'))
                ->isInstanceOf('\Iridium\Components\EventDispatcher\Event\Event');
    }

}
