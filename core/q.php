<?php
/*
	LiveStats Library
	
	version: 2.0
	query engines: Goldsource, Source, SA:MP
	protocol: 47, 48
	author: nr913
	
	visit www.freakz.ro
*/

define('HL_PACKET', -1);
define('HL_PACKET_SPLITTED', -2);
define('SAMP_PACKET', 1347240275);

class Player {
	var $ID;
	var $Name;
	var $Score;
	var $TimePlayed;
	var $Ping;
}

class Server {
	var $Type = 0;
	var $ProtocolVersion;
	var $Address;
	var $Hostname;
	var $Map;
	var $Directory;
	var $Description;
	var $AppID;
	var $MaxPlayers;
	var $PlayerCount;
	var $BotCount;
	var $Dedicated;
	var $OS;
	var $PasswordProtected;
	var $Secured;
	var $GameMode;
	var $WitnessCount;
	var $WitnessTime;
	var $GameVersion;
	var $Players = array();
	var $Rules = array();
}

class Time {
	var $Hours;
	var $Minutes;
	var $Seconds;
	
	function __construct($seconds) {
		$this->Hours = floor($seconds / 3600);
		$this->Minutes = floor($seconds / 60) % 60;
		$this->Seconds = floor($seconds) % 60;
	}
	
	function __toString() {
		$hours = ($this->Hours < 10 ? '0' . $this->Hours : $this->Hours);
		$minutes = ($this->Minutes < 10 ? '0' . $this->Minutes : $this->Minutes);
		$seconds = ($this->Seconds < 10 ? '0' . $this->Seconds : $this->Seconds);
		return "$hours:$minutes:$seconds";
	}
}

class LSError extends Exception {
	var $ErrorMessage;
	var $ErrorCode;
	
	function __construct($errcode, $errmsg) {
		$this->ErrorCode = $errcode;
		$this->ErrorMessage = $errmsg;
	}
}

class Utils {
	static function getTime() {
		list($u, $s) = explode(' ', microtime());
		return ((float)$u + (float)$s);
	}
	
	static function getString($packet, &$offset, $length = -1) {
		$len = strlen($packet);
		$i = 0;
		if ($length == -1) {
			while ($offset + $i < $len && $packet[$offset + $i] != "\x00")
				$i++;
			$offset += $i + 1;
			return substr($packet, $offset - $i - 1, $i);
		} else {
			$i = $length;
			if ($len - $offset < $length)
				$i = $len - $offset;
			$offset += $i;
			return substr($packet, $offset - $i, $i);
		}
	}
	
	static function getByte($packet, &$offset) {
		if ($offset >= strlen($packet))
			return 0;
		$char = unpack('cchar', $packet[$offset]);
		$offset++;
		return $char['char'];
	}
	
	static function getShort($packet, &$offset) {
		if ($offset + 2 >= strlen($packet))
			return 0;
		$short = unpack('sshort', substr($packet, $offset, 2));
		$offset += 2;
		return $short['short'];
	}

	static function getInt($packet, &$offset) {
		if ($offset + 4 >= strlen($packet))
			return 0;
		$int = unpack('iint', substr($packet, $offset, 4));
		$offset += 4;
		return $int['int'];
	}
	
	static function getFloat($packet, &$offset) {
		if ($offset + 4 >= strlen($packet))
			return ((float)0.0);
		$float = unpack('ffloat', substr($packet, $offset, 4));
		$offset += 4;
		return ((float)$float['float']);
	}
}

class LiveStats {
	var $IP;
	var $Port;
	var $Sock;
	var $Challenge = "\xFF\xFF\xFF\xFF";
	var $HaveInfo = false;
	var $HavePlayer = false;
	var $HaveRules = false;
	var $ResendPlayer = false;
	var $ResendRules = false;
	var $SendQueue = array();
	var $MaxExecutionTime = 2;
	var $SplittedPackets = array();
	var $SAMPHeader = "SAMP";
	
	var $Server;
	
	public function __construct($hostname, $port = 27015) {
		$this->IP = gethostbyname($hostname);
		$this->Port = $port;
		
		$errno = 0; $errstr = '';
		$this->Sock = @stream_socket_client("udp://{$this->IP}:{$this->Port}", $errno, $errstr, 5);
		
		if ($this->Sock === false)
			throw new LSError(1, "Could not connect to specified host [$errno]: $errstr.");

		if (stream_set_blocking($this->Sock, false) == false)
			throw new LSError(2, "Could not set to non-blocking mode.");
		
		if (stream_set_timeout($this->Sock, 3) == false)
			throw new LSError(3, "Could not set timeout to 10 seconds.");
		
		$this->Server = new Server;
		$this->Server->Address = "{$this->IP}:{$this->Port}";
		
		$ip = explode('.', $this->IP);
		$this->SAMPHeader .= chr($ip[0]) . chr($ip[1]) . chr($ip[2]) . chr($ip[3]) . chr($this->Port & 0xFF) . chr(($this->Port >> 8) & 0xFF);
	}
	
	function onSplittedPacketReceived($packet) {
		$offset = 4;
		$ReqID = Utils::getInt($packet, $offset);
		if (!isset($this->SplittedPackets[$ReqID])) {
			$this->SplittedPackets[$ReqID] = array(
				'Data' => array(),
				'Type' => -1,
				'Packets' => -1
			);
		}
		$this->SplittedPackets[$ReqID]['Data'][] = $packet;
		if (count($this->SplittedPackets[$ReqID]['Data']) == 2) {
			$offset = 8;
			$FB1 = Utils::getByte($this->SplittedPackets[$ReqID]['Data'][0], $offset);
			$offset = 8;
			$FB2 = Utils::getByte($this->SplittedPackets[$ReqID]['Data'][1], $offset);
			if ($FB1 != $FB2) {
				$this->SplittedPackets[$ReqID]['Type'] = 0;
				$this->SplittedPackets[$ReqID]['Packets'] = ($FB1<<4)>>4;
			} else {
				$this->SplittedPackets[$ReqID]['Type'] = 1;
				$this->SplittedPackets[$ReqID]['Packets'] = $FB1;
			}
		}
		if (count($this->SplittedPackets[$ReqID]['Data']) == $this->SplittedPackets[$ReqID]['Packets']) {
			if ($this->SplittedPackets[$ReqID]['Type'] == 0) {
				for ($i = 0; $i < $this->SplittedPackets[$ReqID]['Packets']; $i++) {
					$offset = 8;
					$PckID = Utils::getByte($this->SplittedPackets[$ReqID]['Data'][$i], $offset)>>4;
					if ($i != $PckID) {
						$aux = $this->SplittedPackets[$ReqID]['Data'][$PckID];
						$this->SplittedPackets[$ReqID]['Data'][$PckID] = $this->SplittedPackets[$ReqID]['Data'][$i];
						$this->SplittedPackets[$ReqID]['Data'][$i] = $aux;
						$i--;
					}
				}
				$packet = "\xFF\xFF\xFF\xFF" . substr($this->SplittedPackets[$ReqID]['Data'][0], 13);
				for ($i = 1; $i < $this->SplittedPackets[$ReqID]['Packets']; $i++)
					$packet .= substr($this->SplittedPackets[$ReqID]['Data'][$i], 9);
			} else {
				for ($i = 0; $i < $this->SplittedPackets[$ReqID]['Packets']; $i++) {
					$offset = 9;
					$PckID = Utils::getByte($this->SplittedPackets[$ReqID]['Data'][$i], $offset);
					if ($i != $PckID) {
						$aux = $this->SplittedPackets[$ReqID]['Data'][$PckID];
						$this->SplittedPackets[$ReqID]['Data'][$PckID] = $this->SplittedPackets[$ReqID]['Data'][$i];
						$this->SplittedPackets[$ReqID]['Data'][$i] = $aux;
						$i--;
					}
				}
				$packet = "\xFF\xFF\xFF\xFF" . substr($this->SplittedPackets[$ReqID]['Data'][0], 16);
				for ($i = 1; $i < $this->SplittedPackets[$ReqID]['Packets']; $i++)
					$packet .= substr($this->SplittedPackets[$ReqID]['Data'][$i], 12);
			}
			unset($this->SplittedPackets[$ReqID]);
			$this->onPacketReceived($packet);
		}
	}
	
	function onPacketReceived($packet) {
		$offset = 4;
		switch (Utils::getByte($packet, $offset)) {
			case 0x41: // challenge
				$this->Challenge = substr($packet, 5);
				if ($this->ResendPlayer) {
					$this->ResendPlayer = false;
					$this->requestPlayer();
				}
				if ($this->ResendRules) {
					$this->ResendRules = false;
					$this->requestRules();
				}
				break;
			case 0x44: // player
				if ($this->HavePlayer)
					break;
				$offset = 5;
				$numplayers = Utils::getByte($packet, $offset);
				while ($numplayers) {
					$Player = new Player;
					$Player->ID = Utils::getByte($packet, $offset);
					$Player->Name = Utils::getString($packet, $offset);
					$len = strlen($Player->Name);
					$Player->Score = Utils::getInt($packet, $offset);
					$Player->TimePlayed = new Time(Utils::getFloat($packet, $offset));
					$numplayers--;
					
					array_push($this->Server->Players, $Player);
				}
				$this->HavePlayer = true;
				$this->ResendPlayer = false;
				break;
			case 0x49: // info, source
				if ($this->HaveInfo)
					break;
				$offset = 5;
				$this->Server->ProtocolVersion = Utils::getByte($packet, $offset);
				$this->Server->Hostname = Utils::getString($packet, $offset);
				$this->Server->Map = Utils::getString($packet, $offset);
				$this->Server->Directory = Utils::getString($packet, $offset);
				$this->Server->Description = Utils::getString($packet, $offset);
				$this->Server->AppID = Utils::getShort($packet, $offset);
				$this->Server->PlayerCount = Utils::getByte($packet, $offset);
				$this->Server->MaxPlayers = Utils::getByte($packet, $offset);
				$this->Server->BotCount = Utils::getByte($packet, $offset);
				$this->Server->Dedicated = (Utils::getByte($packet, $offset) == ord('d') ? true : false);
				$this->Server->OS = (Utils::getByte($packet, $offset) == 'w' ? 'Windows' : 'Linux');
				$this->Server->PasswordProtected = (Utils::getByte($packet, $offset) == 1 ? true : false);
				$this->Server->Secured = (Utils::getByte($packet, $offset) == 1 ? true : false);
				$TheShipAppIDs = array(2400, 2401, 2402, 2412, 2430, 2406, 2405, 2403);
				if (in_array($this->Server->AppID, $TheShipAppIDs)) {
					$gm = Utils::getByte($packet, $offset);
					$GameModes = array('Hunt', 'Elimination', 'Duel', 'Deathmatch', 'Team VIP', 'Team Elimination');
					$this->Server->GameMode = (isset($GameModes[$gm]) ? $GameModes[$gm] : 'Unknown');
					$this->Server->WitnessCount = Utils::getByte($packet, $offset);
					$this->Server->WitnessTime = Utils::getByte($packet, $offset);
				}
				$this->Server->GameVersion = Utils::getString($packet, $offset);
				$this->HaveInfo = true;
				break;
			case 0x6D: // info, goldsource
				if ($this->HaveInfo)
					break;
				$offset = 5;
				Utils::getString($packet, $offset);
				$this->Server->Hostname = Utils::getString($packet, $offset);
				$this->Server->Map = Utils::getString($packet, $offset);
				$this->Server->Directory = Utils::getString($packet, $offset);
				$this->Server->Description = Utils::getString($packet, $offset);
				$this->Server->PlayerCount = Utils::getByte($packet, $offset);
				$this->Server->MaxPlayers = Utils::getByte($packet, $offset);
				$this->Server->ProtocolVersion = Utils::getByte($packet, $offset);
				$this->Server->Dedicated = (Utils::getByte($packet, $offset) == ord('d') ? true : false);
				$this->Server->OS = Utils::getByte($packet, $offset);
				$this->Server->AppID = ($this->Server->OS == 'Linux' ? 4 : 5);
				$this->Server->PasswordProtected = (Utils::getByte($packet, $offset) == 1 ? true : false);
				$IsMod = Utils::getByte($packet, $offset);
				if ($IsMod == 1) {
					Utils::getString($packet, $offset);
					Utils::getString($packet, $offset);
					Utils::getByte($packet, $offset);
					Utils::getInt($packet, $offset);
					Utils::getInt($packet, $offset);
					Utils::getByte($packet, $offset);
					Utils::getByte($packet, $offset);
				}
				$this->Server->Secured = (Utils::getByte($packet, $offset) == 1 ? true : false);
				$this->Server->BotCount = Utils::getByte($packet, $offset);
				$this->HaveInfo = true;
				break;
			case 0x45: // rules
				if ($this->HaveRules)
					break;
				$offset = 5;
				$rulesnum = Utils::getShort($packet, $offset);
				while ($rulesnum) {
					$Name = Utils::getString($packet, $offset);
					$Value = Utils::getString($packet, $offset);
					$rulesnum--;
					
					$this->Server->Rules[$Name] = $Value;
				}
				$this->HaveRules = true;
				$this->ResendRules = false;
				break;
			default:
				break;
		}
	}
	
	function onSampPacketReceived($packet) {
		$offset = 10;
		switch (Utils::getByte($packet, $offset)) {
			case 0x69: // info
				if ($this->HaveInfo)
					break;
				$this->Server->PasswordProtected = (Utils::getByte($packet, $offset) == 1 ? true : false);
				$this->Server->PlayerCount = Utils::getShort($packet, $offset);
				$this->Server->MaxPlayers = Utils::getShort($packet, $offset);
				$len = Utils::getInt($packet, $offset);
				$this->Server->Hostname = Utils::getString($packet, $offset, $len);
				$len = Utils::getInt($packet, $offset);
				$this->Server->GameMode = Utils::getString($packet, $offset, $len);
				$len = Utils::getInt($packet, $offset);
				$this->Server->Map = Utils::getString($packet, $offset, $len);
				$this->HaveInfo = true;
				if ($this->Server->PlayerCount >= 100)
					$this->HavePlayer = true;
				break;
			case 0x72: // rules
				if ($this->HaveRules)
					break;
				$rulesnum = Utils::getShort($packet, $offset);
				while ($rulesnum) {
					$len = Utils::getByte($packet, $offset);
					$Name = Utils::getString($packet, $offset, $len);
					$len = Utils::getByte($packet, $offset);
					$Value = Utils::getString($packet, $offset, $len);
					$rulesnum--;
					
					$this->Server->Rules[$Name] = $Value;
				}
				$this->HaveRules = true;
				break;
			case 0x64: // player
				if ($this->HavePlayer)
					break;
				$numplayers = Utils::getShort($packet, $offset);
				$lastID = 0;
				$plusID = 0;
				while ($numplayers) {
					$Player = new Player;
					$Player->ID = Utils::getByte($packet, $offset) + $plusID;
					if ($Player->ID < $lastID) {
						$Player->ID += 256;
						$plusID += 256;
					}
					$lastID = $Player->ID;
					$len = Utils::getByte($packet, $offset);
					$Player->Name = Utils::getString($packet, $offset, $len);
					$len = strlen($Player->Name);
					$Player->Score = Utils::getInt($packet, $offset);
					$Player->Ping = Utils::getInt($packet, $offset);
					$numplayers--;
					
					array_push($this->Server->Players, $Player);
				}
				$this->HavePlayer = true;
				break;
			default:
				break;
		}
	}
	
	function requestInfo() {
		array_push($this->SendQueue, "\xFF\xFF\xFF\xFFTSource Engine Query\x00");
		array_push($this->SendQueue, $this->SAMPHeader . "i");
	}
	
	function requestPlayer() {
		array_push($this->SendQueue, "\xFF\xFF\xFF\xFF\x55" . $this->Challenge);
		array_push($this->SendQueue, $this->SAMPHeader . "d");
		if ($this->Challenge == "\xFF\xFF\xFF\xFF")
			$this->ResendPlayer = true;
	}
	
	function requestRules() {
		array_push($this->SendQueue, "\xFF\xFF\xFF\xFF\x56" . $this->Challenge);
		array_push($this->SendQueue, $this->SAMPHeader . "r");
		if ($this->Challenge == "\xFF\xFF\xFF\xFF")
			$this->ResendRules = true;
	}
	
	function GetServer() {
		$start = Utils::getTime();
		
		$this->requestInfo();
		$this->requestPlayer();
		$this->requestRules();
		
		while (!($this->HaveRules && $this->HaveInfo && $this->HavePlayer)) {
			  if (Utils::getTime() - $start >= $this->MaxExecutionTime) { 
            if ($this->HaveInfo && $this->HavePlayer) { 
               $this->HaveRules = true; 
            } else { 
               throw new LSError(4, "Timed out."); 
            } 
         }
			if (count($this->SendQueue) > 0) {
				stream_socket_sendto($this->Sock, array_shift($this->SendQueue));
				
				$meta = stream_get_meta_data($this->Sock);
				if ($meta['timed_out'])
					throw new LSError(5, "Timed out.");
			}
			
			usleep(50000);
			
			$packet = fread($this->Sock, 2048);	
			
			$meta = stream_get_meta_data($this->Sock);
			if ($meta['timed_out'])
				throw new LSError(6, "Timed out.");
			
			if (strlen($packet) > 0) {
				$offset = 0;
				switch (Utils::getInt($packet, $offset)) {
					case HL_PACKET:
						if (!in_array($this->Server->Type, array(0, 1)))
							break;
						$this->Server->Type = 1;
						$this->onPacketReceived($packet);
						break;
					case HL_PACKET_SPLITTED:
						if (!in_array($this->Server->Type, array(0, 1)))
							break;
						$this->Server->Type = 1;
						$this->onSplittedPacketReceived($packet);
						break;
					case SAMP_PACKET:
						if (!in_array($this->Server->Type, array(0, 2)))
							break;
						$this->Server->Type = 2;
						$this->onSampPacketReceived($packet);
						break;
					default:
						break;
				}
			}
		}
		return $this->Server;
	}
}

?>