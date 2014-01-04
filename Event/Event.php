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

namespace Iridium\Components\EventDispatcher\Event;

use Iridium\Components\EventDispatcher;

/**
 * Description of Event
 *
 * @author Mathieu
 */
class Event implements EventInterface
{

    /**
     *
     * @var array
     */
    protected $attachments = array();

    /**
     *
     * @var string
     */
    protected $name = '';

    /**
     *
     * @var EventDispatcher\Dispatcher\Dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @param string $name
     * @param array  $attachments
     *
     * @throws \InvalidArgumentException @see Event::setName()
     */
    public function __construct( $name , array $attachments = array() )
    {
        $this->setName( $name );
        foreach ($attachments as $key => $attach) {
            $this->attach( $attach , $key );
        }
    }

    /**
     *
     * @param string $name
     *
     * @return \Iridium\Components\EventDispatcher\Event\Event
     */
    public function setName($name)
    {
        if ( ! is_string( $name ) || ! preg_match( '#^[a-z0-9.-]+$#i' , $name ) ) {
            throw new \InvalidArgumentException( 'Event name must be a non empty string and must only contain letters, digits, hyphens and dots' );
        }
        $this->name = $name;

        return $this;
    }

    /**
     * Add an element to the list of attachments the Event will propagate to handlers
     *
     * If a provided key is already defined, the newer value will replace the older one.
     *
     * @param mixed           $element
     * @param null|string|int $key     any valid array key
     *
     * @return \Iridium\Components\EventDispatcher\Event\Event
     */
    public function attach($element , $key = null)
    {
        if ( is_null( $key ) ) {
            $this->attachments[] = $element;
        } else {
            $this->attachments[ $key ] = $element;
        }

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     *
     * @return EventDispatcher\Dispatcher\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     *
     * @param  \Iridium\Components\EventDispatcher\Event\EventDispatcher\Dispatcher\Dispatcher $dispatcher
     * @return \Iridium\Components\EventDispatcher\Event\Event
     */
    public function setDispatcher(\Iridium\Components\EventDispatcher\Dispatcher\DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->getAttachments() );
    }

}
