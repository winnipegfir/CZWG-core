<?php

namespace App\Classes;

enum VatsimRating : int {
    case Inactive       = -1;
    case Suspended      = 0;
    case Observer       = 1;
    case Student1       = 2;
    case Student2       = 3;
    case Student3       = 4;
    case Controller1    = 5;
    case Controller2    = 6; // Not used
    case Controller3    = 7;
    case Instructor1    = 8;
    case Instructor2    = 9; // Not used
    case Instructor3    = 10;
    case Supervisor     = 11;
    case Administrator  = 12;

    public function getShortName(): string {
        return match($this)
        {
            self::Inactive =>       'INA',
            self::Suspended =>      'SUS',
            self::Observer =>       'OBS',
            self::Student1 =>       'S1',
            self::Student2 =>       'S2',
            self::Student3 =>       'S3',
            self::Controller1 =>    'C1',
            self::Controller2 =>    'C2',
            self::Controller3 =>    'C3',
            self::Instructor1 =>    'I1',
            self::Instructor2 =>    'I2',
            self::Instructor3 =>    'I3',
            self::Supervisor =>     'SUP',
            self::Administrator =>  'ADM'
        };
    }

    public function getLongName(): string {
        return match($this)
        {
            self::Inactive =>       'Inactive',
            self::Suspended =>      'Suspended',
            self::Observer =>       'Pilot/Observer',
            self::Student1 =>       'Tower Trainee',
            self::Student2 =>       'Tower Controller',
            self::Student3 =>       'TMA Controller',
            self::Controller1 =>    'Enroute Controller',

            self::Controller2,
            self::Controller3 =>    'Senior Controller',

            self::Instructor1 =>    'Instructor',

            self::Instructor2,
            self::Instructor3 =>    'Senior Instructor',

            self::Supervisor =>     'Supervisor',
            self::Administrator =>  'Administrator'
        };
    }
}
