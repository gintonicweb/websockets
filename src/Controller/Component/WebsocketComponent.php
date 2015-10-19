<?php
namespace Websockets\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Network\Response;
use Cake\Utility\Hash;
use Websockets\Websocket\Trigger;

/**
 * Websocket component
 *
 * Hook controller actions to a Websockets front-end
 * implementing (WAMP)
 */
class WebsocketComponent extends Component
{

    /**
     * Reference to the current controller.
     *
     * @var \Cake\Controller\Controller
     */
    protected $_controller;

    /**
     * Reference to the current event manager.
     *
     * @var \Cake\Event\EventManager
     */
    protected $_eventManager;

    /**
     * Components settings.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Constructor
     *
     * @param \Cake\Controller\ComponentRegistry $collection A ComponentCollection this component
     *   can use to lazy load its components.
     * @param array $config Array of configuration settings.
     */
    public function __construct(ComponentRegistry $collection, $config = [])
    {
        $this->_controller = $collection->getController();
        $this->_eventManager = $this->_controller->eventManager();
        parent::__construct($collection, $config);
    }

    /**
     * Callback fired before the output is sent to the browser and launches the
     * event on websockets if need be.
     *
     * @param \Cake\Controller\ComponentRegistry $collection A ComponentCollection
     * this component can use to lazy load its components.
     */
    public function shutdown(Event $event)
    {
        $action = $event->subject()->request->action;

        if (!isset($event->subject()->viewVars['_ws'])) {
            return;
        }

        $_ws = $event->subject()->viewVars['_ws'];
        $data = (isset($_ws) && isset($_ws['data'])) ? $_ws['data'] : null;
        $users = (isset($_ws) && isset($_ws['users'])) ? $_ws['users'] : null;

        if (get_class($users) === 'Cake\ORM\ResultSet') {
            $users = $users->extract('id')->toArray();
        } else {
            // not implemented yet
        }

        $trigger = new Trigger($this->_controller);
        $trigger->publish($data, $users);
    }
}
