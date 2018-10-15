<?php

require_once '../application/game/Game.php';

class Router {
	
	private $game;
	
	public function __construct() {
		$options = new stdClass();

		$options->map = [
			[0, 0, 0], [0, 0, 0], [0, 0, 0],
		];
		$options->snakes = [
			(object) array (
				'id' => 12,
				'name' => 'Vasya',
				'body' => [
					(object) array(
						'x' => 0,
						'y' => 0,
					),
				],
				'direction' => 'left',
			),
		];
		$options->foods = [
			(object) array( 'x' => 2, 'y' => 0, 'value' => 2 )
		];

		$this->game = new Game($options);

		//$COMMAND = $game->getCommand();
		//print_r($this->game->executeCommand($COMMAND->CHANGE_DIRECTION, (object) [ 'id' => 12, 'direction' => 'left']));
	}
	
	public function answer($options) {
		$method = $options->method;
		if ( $method ) {
			// �������� ������ ������
			$COMMAND = $this->game->getCommand();
			foreach ( $COMMAND as $command ) {
				if ( $command === $method ) {
					unset($options->method);
					if ($this->game->executeCommand($method, $options) {
						return $this->game->getStruct();
					}
					return $this->game->executeCommand($method, $options);
				}
			}
			
			return $COMMAND;
		}
		return false;
	}	

}