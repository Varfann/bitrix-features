<?

/**
 * @param mixed      $data
 * @param bool|false $die
 * @param string     $msg
 * @param int|string $color
 */
function dbg($data, $die = false, $msg = null, $color = 2) {
    $debug = new \DoctorNet\Debug();
    $debug($data, $die, ['message' => $msg, 'color' => $color]);
}

/**
 * @param mixed      $data
 * @param bool|false $die
 * @param string     $msg
 */
function dbg2log($data, $die = false, $msg = null) {
    $debug = new \DoctorNet\Debug($data);
    $debug->setMessage($msg)->outToEventLog();
    if ($die) {
        $debug->terminate();
    }
}
