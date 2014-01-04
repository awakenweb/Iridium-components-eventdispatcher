<?php

/*
 * The MIT License
 *
 * Copyright 2013 Mathieu.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Iridium\Components\EventDispatcher\Dispatcher;

use Iridium\Components\EventDispatcher\Event;

class Dispatcher implements DispatcherInterface
{

    /**
     *
     * @var array
     */
    protected $handlers = array();

    /**
     * Register a handler to be called when a specific event is triggered
     *
     * You can set a priority to specify the order the handlers will be called in
     *
     * @param string    $eventName
     * @param \callable $handler
     * @param int       $priority
     *
     * @return \Iridium\Components\EventDispatcher\Dispatcher\Dispatcher
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function subscribeToEvent($eventName , $handler , $priority = 10)
    {
        if ( ! is_string( $eventName ) ) {
            $type = gettype( $eventName );
            throw new \InvalidArgumentException( "Event name must be a string, $type given" );
        }
        if ( ! preg_match( '#^[a-zA-Z0-9.\-]+$#i' , $eventName ) ) {
            throw new \InvalidArgumentException( "Event name does not respect naming convention" );
        }
        if ( ! is_callable( $handler ) ) {
            throw new \InvalidArgumentException( "Handler for '$eventName' must be a valid callable callback" );
        }
        if ( ! is_int( $priority ) ) {
            $type = gettype( $priority );
            throw new \InvalidArgumentException( "Priority must be an integer, $type given" );
        }
        if ( ! isset( $this->handlers[ $eventName ] ) ) {
            $this->handlers[ $eventName ] = array();
        }
        if ( isset( $this->handlers[ $eventName ][ $priority ] ) ) {
            throw new \UnexpectedValueException( "An event handler is already defined at priority number '$priority'" );
        }
        $this->handlers[ $eventName ][ $priority ] = $handler;

        return $this;
    }

    /**
     * Dispatch an event to all the handlers that registered for this specific event.
     *
     * When the method is called, it will return the event, so you can directly
     * call other methods of the event
     *
     * @param  string|\Iridium\Components\EventDispatcher\Event\EventInterface $event
     * @return \Iridium\Components\EventDispatcher\Event\EventInterface
     *
     * @throws \InvalidArgumentException
     */
    public function dispatch($event)
    {
        if ( ! (is_string( $event ) || ($event instanceOf Event\EventInterface)) ) {
            $type = gettype( $event );
            if ($type === 'object') {
                $type = get_class( $event );
            }
            throw new \InvalidArgumentException( "Event must be a string or a EventDispatcher Event object. $type given" );
        }
        // if a string is given, automatically converts it to a Event object with
        // the string as event name
        if ( is_string( $event ) ) {
            $event = new Event\Event( $event );
        }

        $event->setDispatcher( $this );

        if ( isset( $this->handlers[ $event->getName() ] ) ) {
            foreach ( $this->handlers[ $event->getName() ] as $handler ) {
                call_user_func_array( $handler , array( $event ) );
            }
        }

        return $event;
    }

}
