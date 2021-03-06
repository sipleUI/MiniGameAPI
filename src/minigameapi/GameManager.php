<?php

namespace minigameapi;

use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class GameManager {
	private $games = [];
	private $miniGameApi;
	public function __construct(MiniGameApi $miniGameApi) {
		$this->miniGameApi = $miniGameApi;
	}
	public function broadcastMessageToGames(string $message){
		foreach($this->getGames() as $game) {
			$game->broadcastMessage($message);
		}
	}
	public function getMiniGameApi() : MiniGameApi {
		return $this->miniGameApi;
	}
	public function submitGame(Game $game) : bool {
		if(!is_null($this->getGame($game->getName()))) return false;
		$this->games[] = $game;
		return true;
	}
	public function removeGame(string $gameName) {
		foreach($this->getGames() as $key => $game){
			if($game->getName() == $gameName) {
				if($game->isRunning()) $game->end(Game::END_KILLED_GAME);
				unset($this->games[$key]);
			}
		}
		$this->games = array_values($this->games);
	}
	public function getGames() : array{
		return $this->games;
	}
	public function getGame(string $gameName) : ?Game{
		foreach($this->getGames() as $game) {
			if($game->getName() == $gameName) return $game;
		}
		return null;
	}
	public function getTeams() : array {
		$result = [];
		foreach($this->getGames() as $game) {
			$result = array_merge($result,$game->getTeams());
		}
		return $result;
	}
	public function getPlayers() : array{
		$result = [];
		foreach($this->getGames() as $game){
			$result = array_merge($result,$game->getPlayers());
		}
		return $result;
	}
	public function removePlayer(Player $player) : bool{
		foreach($this->getGames() as $game) {
			if($game->removePlayer($player)) return true;
		}
		return false;
	}
	public function quitPlayer(Player $player) : bool{
		foreach($this->getGames() as $game) {
			if($game->quitPlayer($player)) return true;
		}
		return false;
	}
	public function getJoinedGame(Player $player) : ?Game {
		foreach ($this->getGames() as $game) {
			if ($game->isInGame($player)) return $game;
		}
		return null;
	}
	public function update(int $updateCycle) {
		foreach($this->getGames() as $game) {
			$game->update($updateCycle);
		}
	}
}
