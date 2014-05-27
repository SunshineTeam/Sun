<php

final class Version {

    public $Major = 0;
    public $Minor = 0;
    public $Build = 0;
    public $Revision = 0;

    public function CompareTo (Version $Value) {
        return version_compare ($Value -> ToString (4), $this -> ToString (4));

    }

    public function Equals ($Obj) {
        return $Obj instanceof Version;

    }

    public function GetClone () {
        return clone $this;

    }

    public function GetHashCode () {
        require_once '../../Application/Configuration/Patterns.php';
        return hash (Patterns::DEFAULT_HASHING_ALGORITHM, $this -> ToString (4));

    }

    public function GetType () {
        return gettype ($this);

    }

    public static function Parse ($Input) {
        if (is_null ($Input)) {
            require_once '../../Library/Exception/ArgumentNullException.php';
            throw new ArgumentNullException ('"Input" é uma referência nula.', E_ERROR);
        }

        $ListOfArguments = array_map ('intval', explode ('.', $Input));
        $NumberOfArguments = count ($ListOfArguments);

        if (!filter_var ($NumberOfArguments, FILTER_VALIDATE_INT, array (
                    'options' => array (
                            'max_range' => 4,
                            'min_range' => 2
                )))) {
            require_once '../../Library/Exception/ArgumentException.php';
            throw new ArgumentException ('"Input" tem menos de dois ou mais dos quatro componentes de versão.', E_ERROR);
        }

        foreach ($ListOfArguments as $Argument) {
            if (!is_integer ($Argument)) {
                require_once '../../Library/Exception/FormatException.php';
                throw new FormatException ('Pelo menos um componente em "Input" não é um número inteiro.', E_ERROR);
            }

            if ($Argument < 0) {
                require_once '../../Library/Exception/ArgumentOutOfRangeException.php';
                throw new ArgumentOutOfRangeException ('Pelo menos um componente em "Input" é menor que zero.', E_ERROR);
            }

            if ($Argument > PHP_INT_MAX) {
                throw new OverflowException ('Pelo menos um componente em "Input" representa um número que é maior do que PHP_INT_MAX.', E_ERROR);
            }
        }

        $ReflectionClass = (new ReflectionClass (self::class)) -> newInstanceWithoutConstructor ();
        $ReflectionClass -> Major = $ListOfArguments[0];
        $ReflectionClass -> Minor = $ListOfArguments[1];
        $ReflectionClass -> Build = isset ($ListOfArguments[2]) ? $ListOfArguments[2] : -1;
        $ReflectionClass -> Revision = isset ($ListOfArguments[3]) ? $ListOfArguments[3] : -1;

        unset ($ListOfArguments, $NumberOfArguments, $Argument);

        return $ReflectionClass;

    }

    public function ToString ($FieldCount) {
        if (intval ($FieldCount) < 0 || intval ($FieldCount) > 4) {
            require_once '../../Library/Exception/ArgumentException.php';
            throw new ArgumentException ('"FieldCount" é menor que 0, ou maior que 4.', E_ERROR);
        } else {
            switch ($FieldCount) {
                case 1:
                case 2:
                case 3:
                case 4:
                    $PropertyName = array_keys (get_class_vars (self::class));
                    for ($Id = 0; $Id < $FieldCount; $Id++) {
                        if (isset ($Return)) {
                            $Return .= '.' . $this -> {$PropertyName[$Id]};
                        } else {
                            $Return = "{$this -> Major}";
                        }
                    }
                    break;

                default :
                    $Return = '';
                    break;
            }

            return str_replace (-1, 0, $Return);
        }

    }

    public static function TryParse ($Input, &$Result) {
        try {
            $Result = self::Parse ($Input);
            $Return = TRUE;
        } catch (Exception $Ex) {
            if ($Ex -> getCode () == E_ERROR) {
                $Return = FALSE;
            }
        } finally {
            return $Return;
        }

    }

    public function __construct ($Version) {
        try {
            $Version = self::Parse ($Version);

            $this -> Major = $Version -> Major;
            $this -> Minor = $Version -> Minor;
            $this -> Build = $Version -> Build;
            $this -> Revision = $Version -> Revision;

            unset ($Version);
        } catch (Exception $Ex) {
            throw $Ex;
        }

    }

    public function __toString () {
        return $this -> ToString (4);

    }
}
