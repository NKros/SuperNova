<?php

/**
 * Class Fleet
 *
 * @property int dbId
 * @property int playerOwnerId
 * @property int group_id
 * @property int mission_type
 * @property int target_owner_id
 * @property int is_returning
 *
 * @property int time_launch
 * @property int time_arrive_to_target
 * @property int time_mission_job_complete
 * @property int time_return_to_source
 *
 * @property int fleet_start_planet_id
 * @property int fleet_start_galaxy
 * @property int fleet_start_system
 * @property int fleet_start_planet
 * @property int fleet_start_type
 *
 * @property int fleet_end_planet_id
 * @property int fleet_end_galaxy
 * @property int fleet_end_system
 * @property int fleet_end_planet
 * @property int fleet_end_type
 *
 */
class Fleet extends UnitContainer {


  // DBRow inheritance *************************************************************************************************

  /**
   * Table name in DB
   *
   * @var string
   */
  protected static $_table = 'fleets';
  /**
   * Name of ID field in DB
   *
   * @var string
   */
  protected static $_dbIdFieldName = 'fleet_id';
  /**
   * DB_ROW to Class translation scheme
   *
   * @var array
   */
  protected static $_properties = array(
    'dbId'          => array(
      P_DB_FIELD => 'fleet_id',
    ),
    'playerOwnerId' => array(
      P_DB_FIELD => 'fleet_owner',
    ),
    'mission_type'  => array(
      P_DB_FIELD   => 'fleet_mission',
      P_FUNC_INPUT => 'intval',
    ),

    'target_owner_id' => array(
      P_DB_FIELD => 'fleet_target_owner',
    ),
    'group_id'        => array(
      P_DB_FIELD => 'fleet_group',
    ),
    'is_returning'    => array(
      P_DB_FIELD   => 'fleet_mess',
      P_FUNC_INPUT => 'intval',
    ),

    'shipCount' => array(
      P_DB_FIELD  => 'fleet_amount',
// TODO - CHECK !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//      P_FUNC_OUTPUT => 'get_ship_count',
//      P_DB_FIELDS_LINKED => array(
//        'fleet_amount',
//      ),
      P_READ_ONLY => true,
    ),

    'time_launch' => array(
      P_DB_FIELD => 'start_time',
    ),


    'time_arrive_to_target'     => array(
      P_DB_FIELD => 'fleet_start_time',
    ),
    'time_mission_job_complete' => array(
      P_DB_FIELD => 'fleet_end_stay',
    ),
    'time_return_to_source'     => array(
      P_DB_FIELD => 'fleet_end_time',
    ),

    'fleet_start_planet_id' => array(
      P_DB_FIELD   => 'fleet_start_planet_id',
      P_FUNC_INPUT => 'nullIfEmpty',
    ),


    'fleet_start_galaxy' => array(
      P_DB_FIELD => 'fleet_start_galaxy',
    ),
    'fleet_start_system' => array(
      P_DB_FIELD => 'fleet_start_system',
    ),
    'fleet_start_planet' => array(
      P_DB_FIELD => 'fleet_start_planet',
    ),
    'fleet_start_type'   => array(
      P_DB_FIELD => 'fleet_start_type',
    ),

    'fleet_end_planet_id' => array(
      P_DB_FIELD   => 'fleet_end_planet_id',
      P_FUNC_INPUT => 'nullIfEmpty',
    ),
    'fleet_end_galaxy'    => array(
      P_DB_FIELD => 'fleet_end_galaxy',
    ),
    'fleet_end_system'    => array(
      P_DB_FIELD => 'fleet_end_system',
    ),
    'fleet_end_planet'    => array(
      P_DB_FIELD => 'fleet_end_planet',
    ),
    'fleet_end_type'      => array(
      P_DB_FIELD => 'fleet_end_type',
    ),


    'resource_list' => array(
      P_METHOD_EXTRACT   => 'resourcesExtract',
      P_METHOD_INJECT    => 'resourcesInject',
      P_DB_FIELDS_LINKED => array(
        'fleet_resource_metal',
        'fleet_resource_crystal',
        'fleet_resource_deuterium',
      ),
    ),
  );


  // UnitContainer inheritance *****************************************************************************************
  /**
   * Type of this location
   *
   * @var int $locationType
   */
  protected static $locationType = LOC_FLEET;


  // New properties ****************************************************************************************************
  /**
   * `fleet_owner`
   *
   * @var int
   */
  protected $_playerOwnerId = 0;
  /**
   * `fleet_group`
   *
   * @var int
   */
  protected $_group_id = 0;

  /**
   * `fleet_mission`
   *
   * @var int
   */
  protected $_mission_type = 0;

  /**
   * `fleet_target_owner`
   *
   * @var int
   */
  protected $_target_owner_id = null;

  /**
   * @var array
   */
  protected $resource_list = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );


  /**
   * `fleet__mess` - Флаг возвращающегося флота
   *
   * @var int
   */
  protected $_is_returning = 0;
  /**
   * `start_time` - Время отправления - таймштамп взлёта флота из точки отправления
   *
   * @var int $_time_launch
   */
  protected $_time_launch = 0; // `start_time` = SN_TIME_NOW
  /**
   * `fleet_start_time` - Время прибытия в точку миссии/время начала выполнения миссии
   *
   * @var int $_time_arrive_to_target
   */
  protected $_time_arrive_to_target = 0; // `fleet_start_time` = SN_TIME_NOW + $time_travel
  /**
   * `fleet_end_stay` - Время окончания миссии в точке назначения
   *
   * @var int $_time_mission_job_complete
   */
  protected $_time_mission_job_complete = 0; // `fleet_end_stay`
  /**
   * `fleet_end_time` - Время возвращения флота после окончания миссии
   *
   * @var int $_time_return_to_source
   */
  protected $_time_return_to_source = 0; // `fleet_end_time`


  protected $_fleet_start_planet_id = null;
  protected $_fleet_start_galaxy = 0;
  protected $_fleet_start_system = 0;
  protected $_fleet_start_planet = 0;
  protected $_fleet_start_type = PT_ALL;

  protected $_fleet_end_planet_id = null;
  protected $_fleet_end_galaxy = 0;
  protected $_fleet_end_system = 0;
  protected $_fleet_end_planet = 0;
  protected $_fleet_end_type = PT_ALL;

  // Missile properties
  public $missile_target = 0;

  // Fleet event properties
  public $fleet_start_name = '';
  public $fleet_end_name = '';
  public $ov_label = '';
  public $ov_this_planet = '';
  public $event_time = 0;

  protected $resource_delta = array();
  protected $resource_replace = array();


  /**
   * Returns location's player owner ID
   *
   * @return int
   */
  // TODO - REMOVE! TEMPORARY UNTIL THERE BE FULLLY FUNCTIONAL Player CLASS AND FLEETS WOULD BE LOCATED ON PLANET OR PLAYER!!!!!
  public function getPlayerOwnerId() {
    return $this->_dbId;
  }

  /**
   * Fleet constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  public function isEmpty() {
    // TODO: Implement isEmpty() method.
    return false;
  }

//  public function getPlayerOwnerId() {
//    return $this->playerOwnerId;
//  }

  /**
   * Initializes Fleet from user params and posts it to DB
   */
  public function dbInsert() {
    // WARNING! MISSION TIMES MUST BE SET WITH set_times() method!
    // TODO - more checks!
    if(empty($this->_time_launch)) {
      die('Fleet time not set!');
    }

    parent::dbInsert();
  }


  /* FLEET DB ACCESS =================================================================================================*/

  /**
   * LOCK - Lock all records which can be used with mission
   *
   * @param $mission_data
   * @param $fleet_id
   *
   * @return array|bool|mysqli_result|null
   */
  public function dbLockFlying(&$mission_data) {
    // Тупо лочим всех юзеров, чьи флоты летят или улетают с координат отбытия/прибытия $fleet_row
    // Что бы делать это умно - надо учитывать fleet__mess во $fleet_row и в таблице fleets

    $fleet_id_safe = idval($this->_dbId);

    return doquery(
    // Блокировка самого флота
      "SELECT 1 FROM {{fleets}} AS f " .

      // Блокировка всех юнитов, принадлежащих этому флоту
      "LEFT JOIN {{unit}} as unit ON unit.unit_location_type = " . static::$locationType . " AND unit.unit_location_id = f.fleet_id " .

      // Блокировка всех прилетающих и улетающих флотов, если нужно
      // TODO - lock fleets by COORDINATES
      ($mission_data['dst_fleets'] ? "LEFT JOIN {{fleets}} AS fd ON fd.fleet_end_planet_id = f.fleet_end_planet_id OR fd.fleet_start_planet_id = f.fleet_end_planet_id " : '') .
      // Блокировка всех юнитов, принадлежащих прилетающим и улетающим флотам - ufd = unit_fleet_destination
      ($mission_data['dst_fleets'] ? "LEFT JOIN {{unit}} AS ufd ON ufd.unit_location_type = " . static::$locationType . " AND ufd.unit_location_id = fd.fleet_id " : '') .

      ($mission_data['dst_user'] || $mission_data['dst_planet'] ? "LEFT JOIN {{users}} AS ud ON ud.id = f.fleet_target_owner " : '') .
      // Блокировка всех юнитов, принадлежащих владельцу планеты-цели
      ($mission_data['dst_user'] || $mission_data['dst_planet'] ? "LEFT JOIN {{unit}} AS unit_player_dest ON unit_player_dest.unit_player_id = ud.id " : '') .
      // Блокировка планеты-цели
      ($mission_data['dst_planet'] ? "LEFT JOIN {{planets}} AS pd ON pd.id = f.fleet_end_planet_id " : '') .
      // Блокировка всех юнитов, принадлежащих планете-цели - НЕ НУЖНО. Уже залочили ранее, как принадлежащие игроку-цели
//      ($mission_data['dst_planet'] ? "LEFT JOIN {{unit}} AS upd ON upd.unit_location_type = " . LOC_PLANET . " AND upd.unit_location_id = pd.id " : '') .


      ($mission_data['src_user'] || $mission_data['src_planet'] ? "LEFT JOIN {{users}} AS us ON us.id = f.fleet_owner " : '') .
      // Блокировка всех юнитов, принадлежащих владельцу флота
      ($mission_data['src_user'] || $mission_data['src_planet'] ? "LEFT JOIN {{unit}} AS unit_player_src ON unit_player_src.unit_player_id = us.id " : '') .
      // Блокировка планеты отправления
      ($mission_data['src_planet'] ? "LEFT JOIN {{planets}} AS ps ON ps.id = f.fleet_start_planet_id " : '') .
      // Блокировка всех юнитов, принадлежащих планете с которой юниты были отправлены - НЕ НУЖНО. Уже залочили ранее, как принадлежащие владельцу флота
//      ($mission_data['src_planet'] ? "LEFT JOIN {{unit}} AS ups ON ups.unit_location_type = " . LOC_PLANET . " AND ups.unit_location_id = ps.id " : '') .

      "WHERE f.fleet_id = {$fleet_id_safe} GROUP BY 1 FOR UPDATE"
    );
  }


  /* FLEET HELPERS =====================================================================================================*/
  /**
   * Forcibly returns fleet before time outs
   */
  public function commandReturn() {
    $ReturnFlyingTime = ($this->_time_mission_job_complete != 0 && $this->_time_arrive_to_target < SN_TIME_NOW ? $this->_time_arrive_to_target : SN_TIME_NOW) - $this->_time_launch + SN_TIME_NOW + 1;

    $this->markReturned();

    // Считаем, что флот уже долетел TODO
    $this->time_arrive_to_target = SN_TIME_NOW;
    // Убираем флот из группы
    $this->group_id = 0;
    // Отменяем работу в точке назначения
    $this->time_mission_job_complete = 0;
    // TODO - правильно вычслять время возвращения - по проделанному пути, а не по старому времени возвращения
    $this->time_return_to_source = $ReturnFlyingTime;

    // Записываем изменения в БД
    $this->dbSave();

    if($this->_group_id) {
      // TODO: Make here to delete only one AKS - by adding aks_fleet_count to AKS table
      db_fleet_aks_purge();
    }
  }





  /**
   * @return array
   */
  public function target_coordinates_without_type() {
    return array(
      'galaxy' => $this->_fleet_end_galaxy,
      'system' => $this->_fleet_end_system,
      'planet' => $this->_fleet_end_planet,
    );
  }

  /**
   * @return array
   */
  public function target_coordinates_typed() {
    return array(
      'galaxy' => $this->_fleet_end_galaxy,
      'system' => $this->_fleet_end_system,
      'planet' => $this->_fleet_end_planet,
      'type'   => $this->_fleet_end_type,
    );
  }

  /**
   * @return array
   */
  public function launch_coordinates_typed() {
    return array(
      'galaxy' => $this->_fleet_start_galaxy,
      'system' => $this->_fleet_start_system,
      'planet' => $this->_fleet_start_planet,
      'type'   => $this->_fleet_start_type,
    );
  }


  /**
   * Restores fleet or resources to planet
   *
   * @param bool $start
   * @param bool $only_resources
   * @param int  $result
   *
   * @return int
   */
  // TODO - split to functions
  public function RestoreFleetToPlanet($start = true, $only_resources = false, &$result = CACHE_NOTHING) {
    sn_db_transaction_check(true);

    // Если флот уже обработан - не существует или возращается - тогда ничего не делаем
    if($this->isEmpty() || ($this->_is_returning == 1 && $only_resources)) {
      return $result;
    }

    $coordinates = $start ? $this->launch_coordinates_typed() : $this->target_coordinates_typed();

    // Поскольку эта функция может быть вызвана не из обработчика флотов - нам надо всё заблокировать вроде бы НЕ МОЖЕТ!!!
    // TODO Проверить от многократного срабатывания !!!
    // Тут не блокируем пока - сначала надо заблокировать пользователя, что бы не было дедлока
    // TODO поменять на владельца планеты - когда его будут возвращать всегда !!!
    // Узнаем ИД владельца планеты - без блокировки
    $planet_arrival = db_planet_by_vector($coordinates, '', false, 'id_owner');
    // Блокируем пользователя
    $user = db_user_by_id($planet_arrival['id_owner'], true);
    // Блокируем планету
    $planet_arrival = db_planet_by_vector($coordinates, '', true);
    // Блокируем флот

    // TODO - Проверка, что планета всё еще существует на указанных координатах, а не телепортировалась, не удалена хозяином, не уничтожена врагом
    // Флот, который возвращается на захваченную планету, пропадает
    if($start && $this->_is_returning == 1 && $planet_arrival['id_owner'] != $this->_playerOwnerId) {
      $result = RestoreFleetToPlanet($this, $start, $only_resources, $result);

      $this->dbDelete();

      return $result;
    }

    if(!$only_resources) {
      // Landing ships
      $db_changeset = array();

      if($this->_playerOwnerId == $planet_arrival['id_owner']) {
        $fleet_array = $this->shipsGetArray();
        foreach($fleet_array as $ship_id => $ship_count) {
          if($ship_count) {
            $db_changeset['unit'][] = sn_db_unit_changeset_prepare($ship_id, $ship_count, $user, $planet_arrival['id']);
          }
        }

        // Adjusting ship amount on planet
        if(!empty($db_changeset)) {
          db_changeset_apply($db_changeset);
        }
      }
    } else {
      $this->resourcesReset();
      $this->markReturned();
      $this->dbSave();
    }

    // Restoring resources to planet
    if($this->resourcesGetTotal() != 0) {
      $fleet_resources = $this->resourcesGetList();
      db_planet_set_by_id($planet_arrival['id'],
        "`metal` = `metal` + '{$fleet_resources[RES_METAL]}', `crystal` = `crystal` + '{$fleet_resources[RES_CRYSTAL]}', `deuterium` = `deuterium` + '{$fleet_resources[RES_DEUTERIUM]}'");
    }

    if(!$only_resources) {
      $this->dbDelete();
    }

    $result = CACHE_FLEET | ($start ? CACHE_PLANET_SRC : CACHE_PLANET_DST);

    return RestoreFleetToPlanet($this, $start, $only_resources, $result);
  }


  /**
   * Sets object fields for fleet return
   */
  public function markReturned() {
    // TODO - Проверка - а не возвращается ли уже флот?
    $this->is_returning = 1;
  }

  public function isReturning() {
    return 1 == $this->_is_returning;
  }

  public function markReturnedAndSave() {
    $this->markReturned();
    $this->dbSave();
  }

  /**
   * Parses extended unit_array which can include not only ships but resources, captains etc
   *
   * @param $unit_array
   */
  // TODO - separate shipList and unitList
  public function unitsSetFromArray($unit_array) {
    foreach($unit_array as $unit_id => $unit_count) {
      $unit_count = floatval($unit_count);
      if(!$unit_count) {
        continue;
      }

      if($this->isShip($unit_id)) {
        $this->unitList->unitSetCount($unit_id, $unit_count);
      } elseif($this->isResource($unit_id)) {
        $this->resource_list[$unit_id] = $unit_count;
      } else {
        throw new Exception('Trying to pass to fleet non-resource and non-ship ' . var_export($unit_array, true), ERR_ERROR);
      }
    }
  }


  /**
   * Sets fleet timers based on flight duration, time on mission (HOLD/EXPLORE) and fleet departure time.
   *
   * @param int $time_to_travel - flight duration in seconds
   * @param int $time_on_mission - time on mission in seconds
   * @param int $group_sync_delta_time - delta time to adjust fleet arrival time if fleet is a part of group (i.e. ACS)
   * @param int $flight_departure - fleet departure from source planet timestamp. Allows to send fleet in future or in past
   */
  public function set_times($time_to_travel, $time_on_mission = 0, $group_sync_delta_time = 0, $flight_departure = SN_TIME_NOW) {
    $this->_time_launch = $flight_departure;

    $this->_time_arrive_to_target = $this->_time_launch + $time_to_travel + $group_sync_delta_time;
    $this->_time_mission_job_complete = $time_on_mission ? $this->_time_arrive_to_target + $time_on_mission : 0;
    $this->_time_return_to_source = ($this->_time_mission_job_complete ? $this->_time_mission_job_complete : $this->_time_arrive_to_target) + $time_to_travel;
  }


  public function parse_missile_db_row($missile_db_row) {
//    $this->_reset();

    if(empty($missile_db_row) || !is_array($missile_db_row)) {
      return;
    }

//      $planet_start = db_planet_by_vector($irak_original, 'fleet_start_', false, 'name');
//      $irak_original['fleet_start_name'] = $planet_start['name'];
    $this->missile_target = $missile_db_row['primaer'];

    $this->_dbId = -$missile_db_row['id'];
    $this->_playerOwnerId = $missile_db_row['fleet_owner'];
    $this->_mission_type = MT_MISSILE;

    $this->_target_owner_id = $missile_db_row['fleet_target_owner'];

    $this->_group_id = 0;
    $this->_is_returning = 0;

    $this->_time_launch = 0; // $irak['start_time'];
    $this->_time_arrive_to_target = 0; // $irak['fleet_start_time'];
    $this->_time_mission_job_complete = 0; // $irak['fleet_end_stay'];
    $this->_time_return_to_source = $missile_db_row['fleet_end_time'];

    $this->_fleet_start_planet_id = !empty($missile_db_row['fleet_start_planet_id']) ? $missile_db_row['fleet_start_planet_id'] : null;
    $this->_fleet_start_galaxy = $missile_db_row['fleet_start_galaxy'];
    $this->_fleet_start_system = $missile_db_row['fleet_start_system'];
    $this->_fleet_start_planet = $missile_db_row['fleet_start_planet'];
    $this->_fleet_start_type = $missile_db_row['fleet_start_type'];

    $this->_fleet_end_planet_id = !empty($missile_db_row['fleet_end_planet_id']) ? $missile_db_row['fleet_end_planet_id'] : null;
    $this->_fleet_end_galaxy = $missile_db_row['fleet_end_galaxy'];
    $this->_fleet_end_system = $missile_db_row['fleet_end_system'];
    $this->_fleet_end_planet = $missile_db_row['fleet_end_planet'];
    $this->_fleet_end_type = $missile_db_row['fleet_end_type'];

    $this->unitList->unitSetCount(UNIT_DEF_MISSILE_INTERPLANET, $missile_db_row['fleet_amount']);
  }


  /**
   * @param $from
   */
  public function set_start_planet($from) {
    $this->fleet_start_planet_id = intval($from['id']) ? $from['id'] : null;
    $this->fleet_start_galaxy = $from['galaxy'];
    $this->fleet_start_system = $from['system'];
    $this->fleet_start_planet = $from['planet'];
    $this->fleet_start_type = $from['planet_type'];
  }

  /**
   * @param $to
   */
  public function set_end_planet($to) {
    $this->target_owner_id = intval($to['id_owner']) ? $to['id_owner'] : 0;
    $this->fleet_end_planet_id = intval($to['id']) ? $to['id'] : null;
    $this->fleet_end_galaxy = $to['galaxy'];
    $this->fleet_end_system = $to['system'];
    $this->fleet_end_planet = $to['planet'];
    $this->fleet_end_type = $to['planet_type'];
  }



  // UnitList/Ships access ***************************************************************************************************

  // TODO - перекрывать пожже - для миссайл-флотов и дефенс-флотов
  protected function isShip($unit_id) {
    return UnitShip::is_in_group($unit_id);
  }

  /**
   * Set unit count of $unit_id to $unit_count
   * If there is no $unit_id - it will be created and saved to DB on dbSave
   *
   * @param int $unit_id
   * @param int $unit_count
   */
  public function shipSetCount($unit_id, $unit_count = 0) {
    $this->shipAdjustCount($unit_id, $unit_count, true);
  }

  /**
   * Adjust unit count of $unit_id by $unit_count - or just replace value
   * If there is no $unit_id - it will be created and saved to DB on dbSave
   *
   * @param int  $unit_id
   * @param int  $unit_count
   * @param bool $replace_value
   */
  public function shipAdjustCount($unit_id, $unit_count = 0, $replace_value = false) {
    $this->unitList->unitAdjustCount($unit_id, $unit_count, $replace_value);
  }

  public function shipGetCount($unit_id) {
    return $this->unitList->unitGetCount($unit_id);
  }

  public function shipsCountApplyLossMultiplier($ships_lost_multiplier) {
    $this->unitList->unitsCountApplyLossMultiplier($ships_lost_multiplier);
  }

  /**
   * Returns ship list in fleet
   */
  public function shipsGetArray() {
    return $this->unitList->unitsGetArray();
  }

  public function shipsGetTotal() {
    return $this->unitList->unitsCount();
  }

  public function shipsGetCapacity() {
    return $this->unitList->unitsCapacity();
  }

  public function shipsGetHoldFree() {
    return max(0, $this->shipsGetCapacity() - $this->resourcesGetTotal());
  }

  public function shipsGetTotalById($ship_id) {
    return $this->unitList->unitsCountById($ship_id);
  }

  /**
   * Возвращает ёмкость переработчиков во флоте
   *
   * @param array $recycler_info
   *
   * @return int
   *
   * @version 41a6.30
   */
  public function shipsGetCapacityRecyclers(array $recycler_info) {
    $recyclers_incoming_capacity = 0;
    $fleet_data = $this->shipsGetArray();
    foreach($recycler_info as $recycler_id => $recycler_data) {
      $recyclers_incoming_capacity += $fleet_data[$recycler_id] * $recycler_data['capacity'];
    }

    return $recyclers_incoming_capacity;
  }

  // Resources access ***************************************************************************************************

  /**
   * Extracts resources value from db_row
   *
   * @param array $db_row
   *
   * @internal param Fleet $that
   * @version 41a6.30
   */
  protected function resourcesExtract(array &$db_row) {
    $this->resource_list = array(
      RES_METAL     => !empty($db_row['fleet_resource_metal']) ? floor($db_row['fleet_resource_metal']) : 0,
      RES_CRYSTAL   => !empty($db_row['fleet_resource_crystal']) ? floor($db_row['fleet_resource_crystal']) : 0,
      RES_DEUTERIUM => !empty($db_row['fleet_resource_deuterium']) ? floor($db_row['fleet_resource_deuterium']) : 0,
    );
  }

  protected function resourcesInject(array &$db_row) {
    $db_row['fleet_resource_metal'] = $this->resource_list[RES_METAL];
    $db_row['fleet_resource_crystal'] = $this->resource_list[RES_CRYSTAL];
    $db_row['fleet_resource_deuterium'] = $this->resource_list[RES_DEUTERIUM];
  }

  /**
   * Set current resource list from array of units
   *
   * @param array $resource_list
   */
  public function resourcesSet($resource_list) {
    if(!empty($this->propertiesAdjusted['resource_list'])) {
      throw new PropertyAccessException('Property "resource_list" already was adjusted so no SET is possible until dbSave in ' . get_called_class() . '::unitSetResourceList', ERR_ERROR);
    }
    $this->resourcesAdjust($resource_list, true);
  }

  /**
   * Updates fleet resource list with deltas
   *
   * @param $resource_delta_list
   */
  public function resourcesAdjust($resource_delta_list, $replace_value = false) {
    !is_array($resource_delta_list) ? $resource_delta_list = array() : false;

    foreach($resource_delta_list as $resource_id => $unit_delta) {
      if(!UnitResourceLoot::is_in_group($resource_id) || !($unit_delta = floor($unit_delta))) {
        // Not a resource or no resources - continuing
        continue;
      }

      if($replace_value) {
        $this->resource_list[$resource_id] = $unit_delta;
      } else {
        $this->resource_list[$resource_id] += $unit_delta;
        // Preparing changes
        $this->resource_delta[$resource_id] += $unit_delta;
      }

      // Check for negative unit value
      if($this->resource_list[$resource_id] < 0) {
        // TODO
        throw new Exception('Resource ' . $resource_id . ' will become negative in ' . get_called_class() . '::unitAdjustResourceList', ERR_ERROR);
      }
    }

    $this->propertiesAdjusted['resource_list'] = 1;

  }

  public function resourcesGetTotal() {
    return empty($this->resource_list) || !is_array($this->resource_list) ? 0 : array_sum($this->resource_list);
  }

  /**
   * @param array $rate
   *
   * @return float
   */
  public function resourcesGetTotalInMetal(array $rate) {
    return
      $this->resource_list[RES_METAL] * $rate[RES_METAL]
      + $this->resource_list[RES_CRYSTAL] * $rate[RES_CRYSTAL] / $rate[RES_METAL]
      + $this->resource_list[RES_DEUTERIUM] * $rate[RES_DEUTERIUM] / $rate[RES_METAL];
  }

  /**
   * Returns resource list in fleet
   */
  // TODO
  public function resourcesGetList() {
    return $this->resource_list;
  }

  public function resourcesReset() {
    $this->resourcesSet(array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    ));
  }

  protected function isResource($unit_id) {
    return UnitResourceLoot::is_in_group($unit_id);
  }

}
