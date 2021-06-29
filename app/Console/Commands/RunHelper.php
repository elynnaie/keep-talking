<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RunHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:helper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keep Talking and Nobody Explodes Helper';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        while (true) {
            $module = $this->choice(
                'Select module',
                [
                    1 => 'Wires',
                    'Button',
                    'Keypad',
                    'Simon Says',
                    'Whos on First',
                    'Memory',
                    'Morse Code',
                    'Complicated Wires',
                    'Wire Sequence',
                    'Maze',
                    'Password',
                ]
            );

            if ($module === '<Quit>') {
                break;
            }

            $this->line("Selected $module module");

            $module = Str::Camel($module);

            $this->$module();

            $this->ask('Press <Enter> to continue...');
        }

        return 0;
    }

    protected function wires()
    {
        $number = $this->choice('How many wires?', [3 => 3, 4, 5, 6]);

        switch ($number) {
            case 3:
                if (!$this->confirm('Are there red wires?')) {
                    $this->info('Cut 2nd wire');
                    return;
                }

                if ($this->confirm('Is the last wire white?')) {
                    $this->info('Cut last wire');
                    return;
                }

                if ($this->confirm('More than one blue wire?')) {
                    $this->info('Cut last blue wire');
                    return;
                }

                $this->info('Cut last wire');
                break;
            case 4:
                $redWires = $this->choice('How many red wires?', [0, 1, '>=2']);
                if ($redWires === '>=2') {
                    $redWires = 2;
                }

                if ($redWires > 1 && $this->confirm('Last digit of S/N is odd?')) {
                    $this->info('Cut last red wire');
                    return;
                }

                if ($redWires == 0 && $this->confirm('Is the last wire yellow?')) {
                    $this->info('Cut 1st wire');
                    return;
                }

                if ($this->confirm('Exactly one blue wire?')) {
                    $this->info('Cut 1st wire');
                    return;
                }

                if ($this->confirm('More than one yellow wire?')) {
                    $this->info('Cut last wire');
                    return;
                }

                $this->info('Cut 2nd wire');
                break;
            case 5:
                if ($this->confirm('Is the last wire black?') && $this->confirm('Last digit of S/N is odd?')) {
                    $this->info('Cut 4th wire');
                    return;
                }

                if ($this->confirm('Exactly 1 red wire?') && $this->confirm('Is there more than 1 yellow wire?')) {
                    $this->info('Cut 1st wire');
                    return;
                }

                if (!$this->confirm('Are there any black wires?')) {
                    $this->info('Cut 2nd wire');
                    return;
                }

                $this->info('Cut 1st wire');
                break;
            case 6:
                $yellowWires = $this->choice('How many yellow wires?', [0, 1, '>=2']);
                if ($yellowWires === '>=2') {
                    $yellowWires = 2;
                }

                if ($yellowWires == 0 && $this->confirm('Last digit of S/N is odd?')) {
                    $this->info('Cut 3rd wire');
                    return;
                }

                if ($yellowWires == 1 && $this->confirm('More than one white wire?')) {
                    $this->info('Cut 4th wire');
                    return;
                }

                if (!$this->confirm('Are there red wires?')) {
                    $this->info('Cut last wire');
                    return;
                }

                $this->info('Cut 4th wire');

                break;
        }
    }

    protected function button()
    {
        $color = $this->choice('Color', ['Blue', 'White', 'Yellow', 'Red', 'Other']);
        $text = $this->choice('Text', ['Abort', 'Detonate', 'Hold', 'Other']);

        if ($color == 'Blue' && $text == 'Abort') {
            return $this->holdButton();
        }

        if ($text == 'Detonate') {
            $batteries = $this->getBatteries();
            if ($batteries > 1) {
                return $this->clickButton();
            }
        }

        if ($color == 'White' && $this->confirm('Lit indicator wih CAR?')) {
            return $this->holdButton();
        }

        if ($this->confirm('Lit indicator with FRK?')) {
            if (!isset($batteries)) {
                $batteries = $this->getBatteries();
            }

            if ($batteries > 2) {
                return $this->clickButton();
            }
        }

        if ($color == 'Yellow') {
            return $this->holdButton();
        }

        if ($color == 'Red' && $text == 'Hold') {
            return $this->clickButton();
        }

        $this->holdButton();
    }

    protected function holdButton()
    {
        $this->info('Press and hold');

        $numbers = [
            'Blue' => 4,
            'Yellow' => 5,
            'Other' => 1,
        ];

        $color = $this->choice('Color', [1 => 'Blue', 'Yellow', 'Other']);

        $this->info("Release when {$numbers[$color]} in any position");
    }

    protected function clickButton()
    {
        $this->info('Click button');
    }

    protected function getBatteries()
    {
        $batteries = $this->choice('Number of batteries?', [0, 1, 2, '>=3']);
        if ($batteries == '>=3') {
            $batteries = 3;
        }

        return $batteries;
    }

    protected function keypad()
    {
        $choices = [];

        $columns = [
            ['O with tail', 'A T', 'lambda', 'zigzag', 'alien', 'H', 'backwards C'],
            ['backwards E', 'O with tail', 'backwards C', 'loop de loop', 'open star', 'H', 'question mark'],
            ['copyright', 'boobs', 'loop de loop', 'X I', 'R', 'lambda', 'open star'],
            ['6', 'paragraph', 'upside down P', 'alien', 'X I', 'question mark', 'smiley face'],
            ['phi', 'smiley face', 'upside down P', 'C', 'paragraph', 'TV snake', 'closed star'],
            ['6', 'backwards E', 'hash', 'A E', 'phi', 'backwards N', 'omega'],
        ];

        $symbols = collect(Arr::flatten($columns))->unique()->sort()->values()->keyBy(function ($item, $index) {
            return $index + 1;
        });

        for ($i=0; $i<4; $i++) {
            $choices[] = $this->choice("Keypad symbol $i?", $symbols->toArray());
        }

        foreach ($columns as $column) {
            $order = [];
            foreach ($column as $symbol) {
                if (in_array($symbol, $choices)) {
                    $order[] = $symbol;
                }
            }

            if (count($order) === 4) {
                foreach ($order as $outputSymbol) {
                    $this->info($outputSymbol);
                }
                return;
            }
        }

        $this->error('A mistake was made! Try again.');
        $this->keypad();
    }

    protected function simonSays()
    {
        $vowel = $this->confirm('Is there a vowel in the serial number?');
        $strikes = $this->choice('Number of strikes?', [0, 1, 2]);

        if ($vowel) {
            switch ($strikes) {
                case 0:
                    $mapping = ['blue', 'red', 'yellow', 'green'];
                    break;
                case 1:
                    $mapping = ['yellow', 'green', 'blue', 'red'];
                    break;
                case 2:
                    $mapping = ['green', 'red', 'yellow', 'blue'];
                    break;
            }
        } else {
            switch ($strikes) {
                case 0:
                    $mapping = ['blue', 'yellow', 'green', 'red'];
                    break;
                case 1:
                    $mapping = ['red', 'blue', 'yellow', 'green'];
                    break;
                case 2:
                    $mapping = ['yellow', 'green', 'blue', 'red'];
                    break;
            }
        }

        $colorIndex = ['red' => 0, 'blue' => 1, 'green' => 2, 'yellow' => 3];
        $colorHistory = [];
        $color = $this->choice('Color', [1 => 'red', 'blue', 'green', 'yellow', 'done']);
        while ($color !== 'done') {
            $colorHistory[] = $mapping[$colorIndex[$color]];
            $this->info(implode(' ', $colorHistory));
            $color = $this->choice('Color', [1 => 'red', 'blue', 'green', 'yellow', 'done']);
        }
    }

    protected function whosOnFirst()
    {
        $labels = collect([
            '<<DONE>>' => null,
            '<BLANK>' => 'A3',
            'YES' => 'A2',
            'FIRST' => 'B1',
            'DISPLAY' => 'B3',
            'OKAY' => 'B1',
            'SAYS' => 'B3',
            'NOTHING' => 'A2',
            'BLANK' => 'B2',
            'NO' => 'B3',
            'LED' => 'A2',
            'LEAD' => 'B3',
            'READ' => 'B2',
            'RED' => 'B2',
            'REED' => 'A3',
            'LEED' => 'A3',
            'HOLD ON' => 'B3',
            'YOU' => 'B2',
            'YOU ARE' => 'B3',
            'YOUR' => 'B2',
            'YOU\'RE' => 'B2',
            'UR' => 'A1',
            'THERE' => 'B3',
            'THEY\'re' => 'A3',
            'THEIR' => 'B2',
            'THEY ARE' => 'A2',
            'SEE' => 'B3',
            'C' => 'B1',
            'CEE' => 'B3',
        ]);

        $mappings = collect([
            'READY' => 'YES, OKAY, WHAT, MIDDLE, LEFT, PRESS, RIGHT, BLANK, READY, NO, FIRST, UHHH, NOTHING, WAIT',
            'FIRST' => 'LEFT, OKAY, YES, MIDDLE, NO, RIGHT, NOTHING, UHHH, WAIT, READY, BLANK, WHAT, PRESS, FIRST',
            'NO' => 'BLANK, UHHH, WAIT, FIRST, WHAT, READY, RIGHT, YES, NOTHING, LEFT, PRESS, OKAY, NO, MIDDLE',
            'BLANK' => 'WAIT, RIGHT, OKAY, MIDDLE, BLANK, PRESS, READY, NOTHING, NO, WHAT, LEFT, UHHH, YES, FIRST',
            'NOTHING' => 'UHHH, RIGHT, OKAY, MIDDLE, YES, BLANK, NO, PRESS, LEFT, WHAT, WAIT, FIRST, NOTHING, READY',
            'YES' => 'OKAY, RIGHT, UHHH, MIDDLE, FIRST, WHAT, PRESS, READY, NOTHING, YES, LEFT, BLANK, NO, WAIT',
            'WHAT' => 'UHHH, WHAT, LEFT, NOTHING, READY, BLANK, MIDDLE, NO, OKAY, FIRST, WAIT, YES, PRESS, RIGHT',
            'UHHH' => 'READY, NOTHING, LEFT, WHAT, OKAY, YES, RIGHT, NO, PRESS, BLANK, UHHH, MIDDLE, WAIT, FIRST',
            'LEFT' => 'RIGHT, LEFT, FIRST, NO, MIDDLE, YES, BLANK, WHAT, UHHH, WAIT, PRESS, READY, OKAY, NOTHING',
            'RIGHT' => 'YES, NOTHING, READY, PRESS, NO, WAIT, WHAT, RIGHT, MIDDLE, LEFT, UHHH, BLANK, OKAY, FIRST',
            'MIDDLE' => 'BLANK, READY, OKAY, WHAT, NOTHING, PRESS, NO, WAIT, LEFT, MIDDLE, RIGHT, FIRST, UHHH, YES',
            'OKAY' => 'MIDDLE, NO, FIRST, YES, UHHH, NOTHING, WAIT, OKAY, LEFT, READY, BLANK, PRESS, WHAT, RIGHT',
            'WAIT' => 'UHHH, NO, BLANK, OKAY, YES, LEFT, FIRST, PRESS, WHAT, WAIT, NOTHING, READY, RIGHT, MIDDLE',
            'PRESS' => 'RIGHT, MIDDLE, YES, READY, PRESS, OKAY, NOTHING, UHHH, BLANK, LEFT, FIRST, WHAT, NO, WAIT',
            'YOU' => 'SURE, YOU ARE, YOUR, YOU\'RE, NEXT, UH HUH, UR, HOLD, WHAT?, YOU, UH UH, LIKE, DONE, U',
            'YOU ARE' => 'YOUR, NEXT, LIKE, UH HUH, WHAT?, DONE, UH UH, HOLD, YOU, U, YOU\'RE, SURE, UR, YOU ARE',
            'YOUR' => 'UH UH, YOU ARE, UH HUH, YOUR, NEXT, UR, SURE, U, YOU\'RE, YOU, WHAT?, HOLD, LIKE, DONE',
            'YOU\'RE' => 'YOU, YOU\'RE, UR, NEXT, UH UH, YOU ARE, U, YOUR, WHAT?, UH HUH, SURE, DONE, LIKE, HOLD',
            'UR' => 'DONE, U, UR, UH HUH, WHAT?, SURE, YOUR, HOLD, YOU\'RE, LIKE, NEXT, UH UH, YOU ARE, YOU',
            'U' => 'UH HUH, SURE, NEXT, WHAT?, YOU\'RE, UR, UH UH, DONE, U, YOU, LIKE, HOLD, YOU ARE, YOUR',
            'UH HUH' => 'UH HUH, YOUR, YOU ARE, YOU, DONE, HOLD, UH UH, NEXT, SURE, LIKE, YOU\'RE, UR, U, WHAT?',
            'UH UH' => 'UR, U, YOU ARE, YOU\'RE, NEXT, UH UH, DONE, YOU, UH HUH, LIKE, YOUR, SURE, HOLD, WHAT?',
            'WHAT?' => 'YOU, HOLD, YOU\'RE, YOUR, U, DONE, UH UH, LIKE, YOU ARE, UH HUH, UR, NEXT, WHAT?, SURE',
            'DONE' => 'SURE, UH HUH, NEXT, WHAT?, YOUR, UR, YOU\'RE, HOLD, LIKE, YOU, U, YOU ARE, UH UH, DONE',
            'NEXT' => 'WHAT?, UH HUH, UH UH, YOUR, HOLD, SURE, NEXT, LIKE, DONE, YOU ARE, UR, YOU\'RE, U, YOU',
            'HOLD' => 'YOU ARE, U, DONE, UH UH, YOU, UR, SURE, WHAT?, YOU\'RE, NEXT, HOLD, UH HUH, YOUR, LIKE',
            'SURE' => 'YOU ARE, DONE, LIKE, YOU\'RE, YOU, HOLD, UH HUH, UR, SURE, U, WHAT?, NEXT, YOUR, UH UH',
            'LIKE' => 'YOU\'RE, NEXT, U, UR, HOLD, DONE, UH UH, WHAT?, UH HUH, YOU, LIKE, SURE, YOU ARE, YOUR',
        ]);

        $labelOptions = $labels->keys()->sort()->values()->keyBy(function ($item, $index) {
            return $index + 1;
        })->toArray();

        $mappingOptions = $mappings->keys()->sort()->values()->keyBy(function ($item, $index) {
            return $index + 1;
        })->toArray();

        $label = $this->choice('Label', $labelOptions);

        while ($label !== '<DONE>') {
            $this->info($labels[$label]);

            $word = $this->choice('Word', $mappingOptions);
            $this->info($mappings[$word]);

            $label = $this->choice('Label', $labelOptions);
        }
    }

    protected function memory()
    {
        $positions = [];
        $labels = [];

        for ($stage=0; $stage<5; $stage++) {
            $display = $this->choice('Display', [1 => 1, 2, 3, 4]);

            if ($stage === 0) {
                switch ($display) {
                    case 1:
                    case 2:
                        $this->info('Position 2');
                        $positions[] = 2;
                        break;
                    case 3:
                        $this->info('Position 3');
                        $positions[] = 3;
                        break;
                    case 4:
                        $this->info('Position 4');
                        $positions[] = 4;
                        break;
                }

                $labels[] = $this->getMemoryValue();
            } elseif ($stage === 1) {
                switch ($display) {
                    case 1:
                        $this->info('Label 4');
                        $labels[] = 4;
                        $positions[] = $this->getMemoryValue();
                        break;
                    case 2:
                    case 4:
                        $this->info("Position $positions[0]");
                        $positions[] = $positions[0];
                        $labels[] = $this->getMemoryValue();
                        break;
                    case 3:
                        $this->info('Position 1');
                        $positions[] = 1;
                        $labels[] = $this->getMemoryValue();
                        break;
                }
            } elseif ($stage === 2) {
                switch ($display) {
                    case 1:
                        $this->info("Label $labels[1]");
                        $labels[] = $labels[1];
                        $positions[] = $this->getMemoryValue();
                        break;
                    case 2:
                        $this->info("Label $labels[0]");
                        $labels[] = $labels[0];
                        $positions[] = $this->getMemoryValue();
                        break;
                    case 3:
                        $this->info('Position 3');
                        $positions[] = 3;
                        $labels[] = $this->getMemoryValue();
                        break;
                    case 4:
                        $this->info('Label 4');
                        $labels[] = 4;
                        $positions[] = $this->getMemoryValue();
                        break;
                }
            } elseif ($stage === 3) {
                switch ($display) {
                    case 1:
                        $this->info("Position $positions[0]");
                        $positions[] = $positions[0];
                        $labels[] = $this->getMemoryValue();
                        break;
                    case 2:
                        $this->info('Position 1');
                        $positions[] = 1;
                        $labels[] = $this->getMemoryValue();
                        break;
                    case 3:
                    case 4:
                        $this->info("Position $positions[1]");
                        $positions[] = $positions[1];
                        $labels[] = $this->getMemoryValue();
                        break;
                }
            } elseif ($stage === 4) {
                switch ($display) {
                    case 1:
                        $this->info("Label $labels[0]");
                        break;
                    case 2:
                        $this->info("Label $labels[1]");
                        break;
                    case 3:
                        $this->info("Label $labels[3]");
                        break;
                    case 4:
                        $this->info("Label $labels[2]");
                        break;
                }
            }
        }
    }

    protected function getMemoryValue()
    {
        return $this->choice('Ask', [1 => 1, 2, 3, 4]);
    }

    protected function morseCode()
    {
        $morseCodeMappings = [
            '.-' => 'a',
            '-...' => 'b',
            '-.-.' => 'c',
            '-..' => 'd',
            '.' => 'e',
            '..-.' => 'f',
            '--.' => 'g',
            '....' => 'h',
            '..' => 'i',
            '.---' => 'j',
            '-.-' => 'k',
            '.-..' => 'l',
            '--' => 'm',
            '-.' => 'n',
            '---' => 'o',
            '.--.' => 'p',
            '--.-' => 'q',
            '.-.' => 'r',
            '...' => 's',
            '-' => 't',
            '..-' => 'u',
            '...-' => 'v',
            '.--' => 'w',
            '-..-' => 'x',
            '-.--' => 'y',
            '--..' => 'z',
        ];

        $words = collect([
            'shell' => 3.505,
            'halls' => 3.515,
            'slick' => 3.522,
            'trick' => 3.532,
            'boxes' => 3.535,
            'leaks' => 3.542,
            'strobe' => 3.545,
            'bistro' => 3.552,
            'flick' => 3.555,
            'bombs' => 3.565,
            'break' => 3.572,
            'brick' => 3.575,
            'steak' => 3.582,
            'sting' => 3.592,
            'vector' => 3.595,
            'beats' => 3.600,
        ]);

        $word = '';

        for ($i=1; $i<5; $i++) {
            $ordinal = str_ordinal($i);
            $letter = $this->ask("Input $ordinal letter (periods \".\" and dashes \"-\")");
            $letter = $morseCodeMappings[$letter];

            $word .= $letter;

            $possibleWords = $words->filter(function ($value, $key) use ($word) {
                return Str::startsWith($key, $word);
            });

            if ($possibleWords->count() === 0) {
                $this->error('A mistake was made! Starting over.');
                return $this->morseCode();
            }

            if ($possibleWords->count() === 1) {
                $this->info($possibleWords->first());
                return;
            }
        }
    }

    protected function complicatedWires()
    {
        $mappings = [
            // No Red (0-7)
            //   No Blue (0-3)
            //     No LED (0-1)
            //       // No Star (0) C
            'C',
            //       // Star (1) C
            'C',
            //     LED (2-3)
            //       // No Star (2) D
            'D',
            //       // Star (3) B
            'B',
            //   Blue (4-7)
            //     No LED (4-5)
            //       // No Star (4) S
            'S',
            //       // Star (5) D
            'D',
            //     LED (6-7)
            //       // No Star (6) P
            'P',
            //       // Star (7) P
            'P',
            // Red (8-15)
            //   No Blue (8-11)
            //     No LED (8-9)
            //       // No Star (8) S
            'S',
            //       // Star (9) C
            'C',
            //     LED (10-11)
            //       // No Star (10) B
            'B',
            //       // Star (11) B
            'B',
            //   Blue (12-15)
            //     No LED (12-13)
            //       // No Star (12) S
            'S',
            //       // Star (13) P
            'P',
            //     LED (14-15)
            //       // No Star (14) S
            'S',
            //       // Star (15) D
            'D',
        ];

        while (true) {
            $red = $this->confirm('Has Red?', true);
            $blue = $this->confirm('Has Blue?', true);
            $led = $this->confirm('LED Lit?', true);
            $star = $this->confirm('Has Star?', true);

            $lookupValue = $star * 1 + $led * 2 + $blue * 4 + $red * 8;
            $mappedValue = $mappings[$lookupValue];

            switch ($mappedValue) {
                case 'C':
                    $this->info('Cut the wire');
                    break;
                case 'D':
                    $this->info('Skip the wire');
                    break;
                case 'S':
                    if (!isset($serialNumber)) {
                        $serialNumber = $this->confirm('Is the last number of the S/N even?', true);
                    }

                    if ($serialNumber) {
                        $this->info('Cut the wire');
                    } else {
                        $this->info('Skip the wire');
                    }

                    break;
                case 'P':
                    if (!isset($parallelPort)) {
                        $parallelPort = $this->confirm('Is there a parallel port?', true);
                    }

                    if ($parallelPort) {
                        $this->info('Cut the wire');
                    } else {
                        $this->info('Skip the wire');
                    }

                    break;
                case 'B':
                    if (!isset($multipleBatteries)) {
                        $multipleBatteries = $this->confirm('Are there multiple batteries?', true);
                    }

                    if ($multipleBatteries) {
                        $this->info('Cut the wire');
                    } else {
                        $this->info('Skip the wire');
                    }

                    break;
            }

            if (!$this->confirm('Continue?', true)) {
                break;
            }
        }
    }

    protected function wireSequence()
    {
        $numRed = $numBlue = $numBlack = 0;

        $reds = ['C', 'B', 'A', 'AC', 'B', 'AC', 'ABC', 'AB', 'B'];
        $blues = ['B', 'AC', 'B', 'A', 'B', 'BC', 'C', 'AC', 'A'];
        $blacks = ['ABC', 'AC', 'B', 'AC', 'B', 'BC', 'AB', 'C', 'C'];

        while (true) {
            $color = $this->choice('Color', [1 => 'Red', 'Blue', 'Black', '<Done>']);

            if ($color === '<Done>') {
                break;
            }

            $position = $this->choice('Position', [1 => 'A', 'B', 'C']);

            $arrayVar = strtolower($color) . 's';
            $numVar = 'num' . $color;
            if (Str::contains($$arrayVar[$$numVar], $position)) {
                $this->info('Cut');
            } else {
                $this->info('Skip');
            }

            $$numVar++;
        }
    }

    protected function maze()
    {
        $this->error('Good luck');
    }

    protected function password()
    {
        $words = collect([
            'about',
            'after',
            'again',
            'below',
            'could',
            'every',
            'first',
            'found',
            'great',
            'house',
            'large',
            'learn',
            'never',
            'other',
            'place',
            'plant',
            'point',
            'right',
            'small',
            'sound',
            'spell',
            'still',
            'study',
            'their',
            'there',
            'these',
            'thing',
            'think',
            'three',
            'water',
            'where',
            'which',
            'world',
            'would',
            'write',
        ]);

        $letters = [];
        for ($index=1; $index<=5; $index++) {
            $ordinal = str_ordinal($index);
            $letters[] = $this->ask("Input $ordinal letters (e.g. \"anmpt\")");

            $possibleWords = $words->filter(function ($word) use ($letters) {
                for ($i=0; $i<count($letters); $i++) {
                    if (!str_contains($letters[$i], $word[$i])) {
                        return false;
                    }
                }

                return true;
            });

            //$this->info("Possible words: " . implode(' ', $possibleWords->toArray()));

            if ($possibleWords->count() === 0) {
                $this->error('A mistake was made! Starting over.');
                return $this->password();
            }

            if ($possibleWords->count() === 1) {
                $this->info($possibleWords->first());
                return;
            }
        }
    }
}
