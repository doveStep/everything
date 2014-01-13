<?php
/* **********************************************************|************************SERVER**DESIGN***********************|************************************************************
/* *                          IPAD                                                         SERVER                                                     IPHONE                           * */
/* * 1. Tablet accesses page, completes form with player data                                                                                                                          *
/* *                                                            2. Server takes form data, initializes game state: creates/                                                            *
/* *                                                               shuffles deck, sets up players, grants hands, sets first                                                            *
/* *                                                               player.                                                                                                             * */
/* *                                                                                                                          3. Phone accesses link, with game_id & uid. Receives hand*
/* *                                                                                                                             state.                                                *
/* *                                                            4. Server verifies, per page load, whether all players have                                                            *
/* *                                                               loaded; when they have, start the game with start_round.                                                            * */
/* * 5. Tablet queries server with uid=host & game_id & round=0                                                                                                                        *
/* *    once/second                                                                                                                                                                    *
/* *                                                            6. If not all phones have accessed: http code 304                                                                      *
/* *                                                             * If all phones have accessed: return current game state                                                              * */
/* |>7. Display current game state: Current black card, player                                                                                                                         *
/* |    names, player scores, cards played/remaining, current                                                                                                                          *
/* |    judge.                                                                                                                                                                         *
/* |                                                                                                                          8. Phone queries server with uid & game_id & round# 1/sec* */
/* |                                                            9. If round is the same as the server's, return http 304                                                               *
/* |                                                             * If round is out of date, return json with current hand                                                              *
/* |GAME LOOP                                                                                                                10. If player is judging, display nothing.                *
/* |                                                                                                                           * If player is playing, display current hand;           *
/* |                                                                                                                             select/play with touchscreen                          * */
/* |                                                           11. Write game state via ajax as players submit cards.                                                                  *
/* |                                                               When the cards submitted equals the numbers of players,                                                             *
/* |                                                               update the game state to judging, and let the judge's                                                               *
/* |                                                               client begin judging.                                                                                               *
/* |                                                                                                                         12. If player is judging, let them interact with the cards*
/* |                                                                                                                             If player is playing, let them just view the          *
/* |                                                                                                                             submissions                                           * */
/* |                                                                                                                         13. Judge selects winner; submits to server               *
/* |                                                           14. Server takes uid=judge & game_id & winner=uid, and writes                                                           *
/* |                                                               new state. Increments point, writes history, sets everyone                                                          *
/* L_________<________________<__________________<____________<    back to correct card total                                                                                          *
/* *                                                                                                                                                                                   * */
/* ************************************************************************************************************************************************************************************* */

class Game {
	public $players = array();
	public $turns = 0;
	public $current_judge;
	public $game_id;
	public $white_deck = array();
	public $black_deck = array();
	public $white_discard = array();
	public $black_discard = array();
	public $current_black_card_id;
	public $current_black_card_text;

	function __construct( $player_array = NULL) {
		$game_id = $this->createGameId();

		$all_cards = $this->createCards();

		$this->white_deck = $this->shuffleDeck( $all_cards['white_cards'] );
		$this->black_deck = $this->shuffleDeck( $all_cards['black_cards'] );

		$black = $this->setCurrentBlackCard();
		$this->current_black_card_id = $black['id'];
		$this->current_black_card_text = $black['text'];
	}

    public function createGameId() {
        $this->game_id = mt_rand(1, 100000);
    }

	public function addPlayerToGame( $player ) {
		$this->players[] = $player;
		return;
	}

	public function getAllPlayersFromGame() {
		return $this->players;
	}

    function shuffle_assoc(&$array) {
        $keys = array_keys($array);
        shuffle($keys);
        foreach($keys as $key) {
            $new[$key] = $array[$key];
        }
        $array = $new;
        return;
    }

	public function setCurrentBlackCard() {
		$card = array_rand( $this->black_deck );
		$text = $this->black_deck[ $card ];
		$pair = array('id' => $card, 'text' => $text);
		unset( $this->black_deck[ $card ]);
		return $pair;
	}

	public function getGameId() {
		return $this->game_id;
	}
	public function getWhiteDeck( $game_id = NULL) {
		return array_keys($this->white_deck);
	}
	public function getBlackDeck( $game_id = NULL) {
		return array_keys($this->black_deck);
	}
	public function addToBlackDiscard( $discarded_card ) {
		$this->black_discard[] = $discarded_card;
		return $this->black_discard;
	}
	public function addToWhiteDiscard( $discarded_card ) {
		$this->white_discard[] = $discarded_card;
		return $this->white_discard;
	}
	public function getBlackDiscard() {
		return $this->black_discard;
	}
	public function getWhiteDiscard() {
		return $this->white_discard;
	}

	public function shuffleDiscardIntoDeck( $color = '') {
        if (sizeof($this->discard) < 1) {
            throw new Exception( array(
                'msg' => 'No cards in either deck or discard',
            ));
        }
        $this->deck = $this->discard;
        $this->discard = array();
        return $this->shuffle_assoc( $this->deck );
    }

    public function createCards() {
		// $white_card_keys = range(1, sizeof(Init::$white_card_vals));
		// $black_card_keys = range(1, sizeof(Init::$black_card_vals));

		// $white_cards = array_combine($white_card_keys, Init::$white_card_vals);
		// $black_cards = array_combine($black_card_keys, Init::$black_card_vals);
		$white_cards = array_flip(array_keys(Init::$white_card_vals));
        $black_cards = array_flip(array_keys(Init::$black_card_vals));

        return array('white_cards' => $white_cards, 'black_cards' => $black_cards);
    }

    public function shuffleDeck( $deck ) {
        $this->shuffle_assoc( $deck );
        return $deck;
    }
}

class Init {
    public static $salt = 'foo!bar@baz#quoz';
    public static $hand_size = 7;
    public static $white_card_vals = array(
'w1' => 'A Gypsy curse.',	'w2' => 'A moment of silence.',	'w3' => 'A sausage festival.',	'w4' => 'An honest cop with nothing left to lose.',	'w5' => 'Famine.',	'w6' => 'Flesh-eating bacteria.',	'w7' => 'Flying sex snakes.',	'w8' => 'Not giving a shit about the Third World.',	'w9' => 'Sexting.',	'w10' => 'Shapeshifters.',	'w11' => 'Porn stars.',	'w12' => 'Raping and pillaging.',	'w13' => '72 virgins.',	'w14' => 'A drive-by shooting.',	'w15' => 'A time travel paradox.',	'w16' => 'Authentic Mexican cuisine.',	'w17' => 'Bling.',	'w18' => 'Consultants.',	'w19' => 'Crippling debt.',	'w20' => 'Daddy issues.',	'w21' => 'The Donald Trump Seal of Approval.',	'w22' => 'Dropping a chandelier on your enemies and riding the rope up.',	'w23' => 'Former President George W. Bush.',	'w24' => 'Full frontal nudity.',	'w25' => 'Hormone injections.',	'w26' => 'Laying an egg.',	'w27' => 'Getting naked and watching Nickelodeon.',	'w28' => 'Pretending to care.',	'w29' => 'Public ridicule.',	'w30' => 'Sharing needles.',	'w31' => 'Boogers.',	'w32' => 'The inevitable heat death of the universe.',	'w33' => 'The miracle of childbirth.',	'w34' => 'The Rapture.',	'w35' => 'Whipping it out.',	'w36' => 'White privilege.',	'w37' => 'Wifely duties.',	'w38' => 'The Hamburglar.',	'w39' => 'AXE Body Spray.',	'w40' => 'The Blood of Christ.',	'w41' => 'Horrifying laser hair removal accidents.',	'w42' => 'BATMAN!!!',	'w43' => 'Agriculture.',	'w44' => 'A robust mongoloid.',	'w45' => 'Natural selection.',	'w46' => 'Coat hanger abortions.',	'w47' => 'Eating all of the cookies before the AIDS bake-sale.',	'w48' => 'Michelle Obama\'s arms.',	'w49' => 'The World of Warcraft.',	'w50' => 'Swooping.',	'w51' => 'Obesity.',	'w52' => 'A homoerotic volleyball montage.',	'w53' => 'Lockjaw.',	'w54' => 'A mating display.',	'w55' => 'Testicular torsion.',	'w56' => 'All-you-can-eat shrimp for $4.99.',	'w57' => 'Domino\'s(c) Oreo(c) Dessert Pizza.',	'w58' => 'Kanye West.',	'w59' => 'Hot cheese.',	'w60' => 'Raptor attacks.',	'w61' => 'Taking off your shirt.',	'w62' => 'Smegma.',	'w63' => 'Alcoholism.',	'w64' => 'A middle-aged man on roller skates.',	'w65' => 'The Care Bear Stare.',	'w66' => 'Bingeing and purging.',	'w67' => 'Oversized lollipops.',	'w68' => 'Self-loathing.',	'w69' => 'Children on leashes.',	'w70' => 'Half-assed foreplay.',	'w71' => 'The Holy Bible.',	'w72' => 'German dungeon porn.',	'w73' => 'Being on fire.',	'w74' => 'Teenage pregnancy.',	'w75' => 'Gandhi.',	'w76' => 'Leaving an awkward voicemail.',	'w77' => 'Uppercuts.',	'w78' => 'Customer service representatives.',	'w79' => 'An erection that lasts longer than four hours.',	'w80' => 'My genitals.',	'w81' => 'Picking up girls at the abortion clinic.',	'w82' => 'Science.',	'w83' => 'Not reciprocating oral sex.',	'w84' => 'Flightless birds.',	'w85' => 'A good sniff.',	'w86' => 'Waterboarding.',	'w87' => 'A balanced breakfast.',	'w88' => 'Historically black colleges.',	'w89' => 'Actually taking candy from a baby.',	'w90' => 'The Make-A-Wish Foundation.',	'w91' => 'A clandestine butt scratch.',	'w92' => 'Passive-aggressive Post-it notes.',	'w93' => 'The Chinese gymnastics team.',	'w94' => 'Switching to Geico.',	'w95' => 'Peeing a little bit.',	'w96' => 'Home video of Oprah sobbing into a Lean Cuisine.',	'w97' => 'Nocturnal emissions.',	'w98' => 'The Jews.',	'w99' => 'My humps.',	'w100' => 'Powerful thighs.',	'w101' => 'Winking at old people.',	'w102' => 'Mr. Clean, right behind you.',	'w103' => 'A gentle caress of the inner thigh.',	'w104' => 'Sexual tension.',	'w105' => 'The forbidden fruit.',	'w106' => 'Skeletor.',	'w107' => 'Fancy Feast.',	'w108' => 'Being rich.',	'w109' => 'Sweet, sweet vengeance.',	'w110' => 'Republicans.',	'w111' => 'A gassy antelope.',	'w112' => 'Natalie Portman.',	'w113' => 'Copping a feel.',	'w114' => 'Kamikaze pilots.',	'w115' => 'Sean Connery.',	'w116' => 'The homosexual agenda.',	'w117' => 'The hardworking Mexican.',	'w118' => 'A falcon with a cap on its head.',	'w119' => 'Altar boys.',	'w120' => 'The Kool-Aid Man.',	'w121' => 'Getting so angry that you pop a boner.',	'w122' => 'Free samples.',	'w123' => 'A big hoopla about nothing.',	'w124' => 'Doing the right thing.',	'w125' => 'The Three-Fifths compromise.',	'w126' => 'Lactation.',	'w127' => 'World peace.',	'w128' => 'RoboCop.',	'w129' => 'Chutzpah.',	'w130' => 'Justin Bieber.',	'w131' => 'Oompa-Loompas.',	'w132' => 'Inappropriate yodeling.',	'w133' => 'Puberty.',	'w134' => 'Ghosts.',	'w135' => 'An asymmetric boob job.',	'w136' => 'Vigorous jazz hands.',	'w137' => 'Fingering.',	'w138' => 'Glenn Beck catching his scrotum on a curtain hook.',	'w139' => 'GoGurt.',	'w140' => 'Police brutality.',	'w141' => 'John Wilkes Booth.',	'w142' => 'Preteens.',	'w143' => 'Scalping.',	'w144' => 'Stifling a giggle at the mention of Hutus and Tutsis.',	'w145' => '"Tweeting."',	'w146' => 'Darth Vader.',	'w147' => 'A sad handjob.',	'w148' => 'Exactly what you\'d expect.',	'w149' => 'Expecting a burp and vomiting on the floor.',	'w150' => 'Adderall(c).',	'w151' => 'Embryonic stem cells.',	'w152' => 'Tasteful sideboob.',	'w153' => 'Panda sex.',	'w154' => 'An icepick lobotomy.',	'w155' => 'Tom Cruise.',	'w156' => 'Mouth herpes.',	'w157' => 'Sperm whales.',	'w158' => 'Homeless people.',	'w159' => 'Third base.',	'w160' => 'Incest.',	'w161' => 'Pac-Man uncontrollably guzzling cum.',	'w162' => 'A mime having a stroke.',	'w163' => 'Hulk Hogan.',	'w164' => 'God.',	'w165' => 'Scrubbing under the folds.',	'w166' => 'Golden showers.',	'w167' => 'Emotions.',	'w168' => 'Licking things to claim them as your own.',	'w169' => 'Pabst Blue Ribbon.',	'w170' => 'The placenta.',	'w171' => 'Spontaneous human combustion.',	'w172' => 'Friends with benefits.',	'w173' => 'Finger painting.',	'w174' => 'Old-people smell.',	'w175' => 'Dying of dysentery.',	'w176' => 'My inner demons.',	'w177' => 'A Super Soaker(c) full of cat pee.',	'w178' => 'Aaron Burr.',	'w179' => 'Cuddling.',	'w180' => 'The chronic.',	'w181' => 'Cockfights.',	'w182' => 'Friendly fire.',	'w183' => 'Ronald Reagan.',	'w184' => 'A disappointing birthday party.',	'w185' => 'A sassy black woman.',	'w186' => 'Mathletes.',	'w187' => 'A tiny horse.',	'w188' => 'William Shatner.',	'w189' => 'Riding off into the sunset.',	'w190' => 'An M. Night Shyamalan plot twist.',	'w191' => 'Jew-fros.',	'w192' => 'Mutually-assured destruction.',	'w193' => 'Pedophiles.',	'w194' => 'Yeast.',	'w195' => 'Grave robbing.',	'w196' => 'Eating the last known bison.',	'w197' => 'Catapults.',	'w198' => 'Poor people.',	'w199' => 'Forgetting the Alamo.',	'w200' => 'The Hustle.',	'w201' => 'The Force.',	'w202' => 'Wiping her butt.',	'w203' => 'Intelligent design.',	'w204' => 'Loose lips.',	'w205' => 'AIDS.',	'w206' => 'Pictures of boobs.',	'w207' => 'The Ubermensch.',	'w208' => 'Sarah Palin.',	'w209' => 'American Gladiators.',	'w210' => 'Getting really high.',	'w211' => 'Scientology.',	'w212' => 'Penis envy.',	'w213' => 'Praying the gay away.',	'w214' => 'Frolicking.',	'w215' => 'Two midgets shitting into a bucket.',	'w216' => 'The KKK.',	'w217' => 'Genghis Khan.',	'w218' => 'Crystal meth.',	'w219' => 'Serfdom.',	'w220' => 'Stranger danger.',	'w221' => 'A Bop It(c).',	'w222' => 'Shaquille O\'Neal\'s acting career.',	'w223' => 'Prancing.',	'w224' => 'Vigilante justice.',	'w225' => 'Overcompensation.',	'w226' => 'Pixelated bukkake.',	'w227' => 'A lifetime of sadness.',	'w228' => 'Racism.',	'w229' => 'Dwarf tossing.',	'w230' => 'Sunshine and rainbows.',	'w231' => 'A monkey smoking a cigar.',	'w232' => 'Flash flooding.',	'w233' => 'Lance Armstrong\'s missing testicle.',	'w234' => 'Dry heaving.',	'w235' => 'The terrorists.',	'w236' => 'Britney Spears at 55.',	'w237' => 'Attitude.',	'w238' => 'Breaking out into song and dance.',	'w239' => 'Leprosy.',	'w240' => 'Gloryholes.',	'w241' => 'Nipple blades.',	'w242' => 'The heart of a child.',	'w243' => 'Puppies!',	'w244' => 'Waking up half-naked in a Denny\'s parking lot.',	'w245' => 'Dental dams.',	'w246' => 'Toni Morrison\'s vagina.',	'w247' => 'The taint; the grundle; the fleshy fun-bridge.',	'w248' => 'Active listening.',	'w249' => 'Ethnic cleansing.',	'w250' => 'The Little Engine That Could.',	'w251' => 'The invisible hand.',	'w252' => 'Waiting \'til marriage.',	'w253' => 'Unfathomable stupidity.',	'w254' => 'Euphoria(c) by Calvin Klein.',	'w255' => 'Re-gifting.',	'w256' => 'Autocannibalism.',	'w257' => 'Erectile dysfunction.',	'w258' => 'My collection of high-tech sex toys.',	'w259' => 'The Pope.',	'w260' => 'White people.',	'w261' => 'Tentacle porn.',	'w262' => 'Glenn Beck convulsively vomiting as a brood of crab spiders hatches in his brain and erupts from his tear ducts.',	'w263' => 'Too much hair gel.',	'w264' => 'Seppuku.',	'w265' => 'Same-sex ice dancing.',	'w266' => 'Cheating in the Special Olympics.',	'w267' => 'Charisma.',	'w268' => 'Keanu Reeves.',	'w269' => 'Sean Penn.',	'w270' => 'Nickelback.',	'w271' => 'A look-see.',	'w272' => 'Pooping back and forth. Forever.',	'w273' => 'Menstruation.',	'w274' => 'Kids with ass cancer.',	'w275' => 'A salty surprise.',	'w276' => 'The South.',	'w277' => 'The violation of our most basic human rights.',	'w278' => 'YOU MUST CONSTRUCT ADDITIONAL PYLONS.',	'w279' => 'Date rape.',	'w280' => 'Being fabulous.',	'w281' => 'Necrophilia.',	'w282' => 'Centaurs.',	'w283' => 'Bill Nye the Science Guy.',	'w284' => 'Black people.',	'w285' => 'Chivalry.',	'w286' => 'Lunchables(c).',	'w287' => 'Bitches.',	'w288' => 'The profoundly handicapped.',	'w289' => 'Heartwarming orphans.',	'w290' => 'MechaHitler.',	'w291' => 'Fiery poops.',	'w292' => 'Another goddamn vampire movie.',	'w293' => 'Tangled Slinkys.',	'w294' => 'The true meaning of Christmas.',	'w295' => 'Estrogen.',	'w296' => 'A zesty breakfast burrito.',	'w297' => 'That thing that electrocutes your abs.',	'w298' => 'Passing a kidney stone.',	'w299' => 'A bleached asshole.',	'w300' => 'Michael Jackson.',	'w301' => 'Cybernetic enhancements.',	'w302' => 'Guys who don\'t call.',	'w303' => 'Smallpox blankets.',	'w304' => 'Masturbation.',	'w305' => 'Classist undertones.',	'w306' => 'Queefing.',	'w307' => 'Concealing a boner.',	'w308' => 'Edible underpants.',	'w309' => 'Viagra.',	'w310' => 'Soup that is too hot.',	'w311' => 'Muhammad (Praise Be Unto Him).',	'w312' => 'Surprise sex!',	'w313' => 'Five-Dollar Footlongs(c).',	'w314' => 'Drinking alone.',	'w315' => 'Dick fingers.',	'w316' => 'Multiple stab wounds.',	'w317' => 'Soiling oneself.',	'w318' => 'Child abuse.',	'w319' => 'Anal beads.',	'w320' => 'Civilian casualties.',	'w321' => 'Pulling out.',	'w322' => 'Robert Downey, Jr.',	'w323' => 'Horse meat.',	'w324' => 'A really cool hat.',	'w325' => 'Kim Jong-il.',	'w326' => 'A stray pube.',	'w327' => 'Jewish fraternities.',	'w328' => 'The token minority.',	'w329' => 'Doin\' it in the butt.',	'w330' => 'Feeding Rosie O\'Donnell.',	'w331' => 'Teaching a robot to love.',	'w332' => 'A can of whoop-ass.',	'w333' => 'A windmill full of corpses.',	'w334' => 'Count Chocula.',	'w335' => 'Wearing underwear inside-out to avoid doing laundry.',	'w336' => 'A death ray.',	'w337' => 'The glass ceiling.',	'w338' => 'A cooler full of organs.',	'w339' => 'The American Dream.',	'w340' => 'Keg stands.',	'w341' => 'When you fart and a little bit comes out.',	'w342' => 'Take-backsies.',	'w343' => 'Dead babies.',	'w344' => 'Foreskin.',	'w345' => 'Saxophone solos.',	'w346' => 'Italians.',	'w347' => 'A fetus.',	'w348' => 'Firing a rifle into the air while balls deep in a squealing hog.',	'w349' => 'Dick Cheney.',	'w350' => 'Amputees.',	'w351' => 'Eugenics.',	'w352' => 'My relationship status.',	'w353' => 'Christopher Walken.',	'w354' => 'Bees?',	'w355' => 'Harry Potter erotica.',	'w356' => 'College.',	'w357' => 'Getting drunk on mouthwash.',	'w358' => 'Nazis.',	'w359' => '8 oz. of sweet Mexican black-tar heroin.',	'w360' => 'Stephen Hawking talking dirty.',	'w361' => 'Dead parents.',	'w362' => 'Object permanence.',	'w363' => 'Opposable thumbs.',	'w364' => 'Racially-biased SAT questions.',	'w365' => 'Jibber-jabber.',	'w366' => 'Chainsaws for hands.',	'w367' => 'Nicolas Cage.',	'w368' => 'Child beauty pageants.',	'w369' => 'Explosions.',	'w370' => 'Sniffing glue.',	'w371' => 'Glenn Beck being harried by a swarm of buzzards.',	'w372' => 'Repression.',	'w373' => 'Roofies.',	'w374' => 'My vagina.',	'w375' => 'Assless chaps.',	'w376' => 'A murder most foul.',	'w377' => 'Giving 110 percent.',	'w378' => 'Her Royal Highness, Queen Elizabeth II.',	'w379' => 'The Trail of Tears.',	'w380' => 'Being marginalized.',	'w381' => 'Goblins.',	'w382' => 'Hope.',	'w383' => 'The Rev. Dr. Martin Luther King, Jr.',	'w384' => 'A micropenis.',	'w385' => 'My soul.',	'w386' => 'A hot mess.',	'w387' => 'Vikings.',	'w388' => 'Hot people.',	'w389' => 'Seduction.',	'w390' => 'An Oedipus complex.',	'w391' => 'Geese.',	'w392' => 'Global warming.',	'w393' => 'New Age music.',	'w394' => 'Hot Pockets.',	'w395' => 'Making a pouty face.',	'w396' => 'Vehicular manslaughter.',	'w397' => 'Women\'s suffrage.',	'w398' => 'A defective condom.',	'w399' => 'Judge Judy.',	'w400' => 'African children.',	'w401' => 'The Virginia Tech Massacre.',	'w402' => 'Barack Obama.',	'w403' => 'Asians who aren\'t good at math.',	'w404' => 'Elderly Japanese men.',	'w405' => 'Exchanging pleasantries.',	'w406' => 'Heteronormativity.',	'w407' => 'Parting the Red Sea.',	'w408' => 'Arnold Schwarzenegger.',	'w409' => 'Road head.',	'w410' => 'Spectacular abs.',	'w411' => 'Figgy pudding.',	'w412' => 'A mopey zoo lion.',	'w413' => 'A bag of magic beans.',	'w414' => 'Poor life choices.',	'w415' => 'My sex life.',	'w416' => 'Auschwitz.',	'w417' => 'A snapping turtle biting the tip of your penis.',	'w418' => 'A thermonuclear detonation.',	'w419' => 'The clitoris.',	'w420' => 'The Big Bang.',	'w421' => 'Land mines.',	'w422' => 'Friends who eat all the snacks.',	'w423' => 'Goats eating cans.',	'w424' => 'The Dance of the Sugar Plum Fairy.',	'w425' => 'Jerking off into a pool of children\'s tears.',	'w426' => 'Man meat.',	'w427' => 'Me time.',	'w428' => 'The Underground Railroad.',	'w429' => 'Poorly-timed Holocaust jokes.',	'w430' => 'A sea of troubles.',	'w431' => 'Lumberjack fantasies.',	'w432' => 'Morgan Freeman\'s voice.',	'w433' => 'Women in yogurt commercials.',	'w434' => 'Natural male enhancement.',	'w435' => 'Being a motherfucking sorcerer.',	'w436' => 'Genital piercings.',	'w437' => 'Passable transvestites.',	'w438' => 'Sexy pillow fights.',	'w439' => 'Balls.',	'w440' => 'Grandma.',	'w441' => 'Friction.',	'w442' => 'Party poopers.',	'w443' => 'Farting and walking away.',	'w444' => 'Being a dick to children.',	'w445' => 'Booby-trapping the house to foil burglars.',	'w446' => 'The Tempur-Pedic Swedish Sleep System(c).',	'w447' => 'Dying.',	'w448' => 'Hurricane Katrina.',	'w449' => 'The gays.',	'w450' => 'The folly of man.',	'w451' => 'Men.',	'w452' => 'The Amish.',	'w453' => 'Pterodactyl eggs.',	'w454' => 'Team-building exercises.',	'w455' => 'A brain tumor.',	'w456' => 'Cards Against Humanity.',	'w457' => 'Fear itself.',	'w458' => 'Lady Gaga.',	'w459' => 'The milk man.',	'w460' => 'A foul mouth.',	'w461' => 'A big black dick.',	'w462' => 'A beached whale.',	'w463' => 'A bloody pacifier.',	'w464' => 'A crappy little hand.',	'w465' => 'A low standard of living.',	'w466' => 'A nuanced critique.',	'w467' => 'Panty raids.',	'w468' => 'A passionate Latino lover.',	'w469' => 'A rival dojo.',	'w470' => 'A web of lies.',	'w471' => 'A woman scorned.',	'w472' => 'Clams.',	'w473' => 'Apologizing.',	'w474' => 'Appreciative snapping.',	'w475' => 'Neil Patrick Harris.',	'w476' => 'Beating your wives.',	'w477' => 'Being a dinosaur.',	'w478' => 'Shaft.',	'w479' => 'Bosnian chicken farmers.',	'w480' => 'Nubile slave boys.',	'w481' => 'Carnies.',	'w482' => 'Coughing into a vagina.',	'w483' => 'Suicidal thoughts.',	'w484' => 'Dancing with a broom.',	'w485' => 'Deflowering the princess.',	'w486' => 'Dorito breath.',	'w487' => 'Eating an albino.',	'w488' => 'Enormous Scandinavian women.',	'w489' => 'Fabricating statistics.',	'w490' => 'Finding a skeleton.',	'w491' => 'Gandalf.',	'w492' => 'Genetically engineered super-soldiers.',	'w493' => 'George Clooney\'s musk.',	'w494' => 'Getting abducted by Peter Pan.',	'w495' => 'Getting in her pants, politely.',	'w496' => 'Gladiatorial combat.',	'w497' => 'Good grammar.',	'w498' => 'Hipsters.',	'w499' => 'Historical revisionism.',	'w500' => 'Insatiable bloodlust.',	'w501' => 'Jafar.',	'w502' => 'Jean-Claude Van Damme.',	'w503' => 'Just the tip.',	'w504' => 'Mad hacky-sack skills.',	'w505' => 'Leveling up.',	'w506' => 'Literally eating shit.',	'w507' => 'Making the penises kiss.',	'w508' => 'Media coverage.',	'w509' => 'Medieval Times Dinner and Tournament.',	'w510' => 'Moral ambiguity.',	'w511' => 'My machete.',	'w512' => 'One thousand Slim Jims.',	'w513' => 'Ominous background music.',	'w514' => 'Overpowering your father.',	'w515' => 'Pistol-whipping a hostage.',	'w516' => 'Quiche.',	'w517' => 'Quivering jowls.',	'w518' => 'Revenge fucking.',	'w519' => 'Ripping into a man\'s chest and pulling out his still-beating heart.',	'w520' => 'Ryan Gosling riding in on a white horse.',	'w521' => 'Santa Claus.',	'w522' => 'Scrotum tickling.',	'w523' => 'Sexual humiliation.',	'w524' => 'Sexy Siamese twins.',	'w525' => 'Slow motion.',	'w526' => 'Space muffins.',	'w527' => 'Statistically validated stereotypes.',	'w528' => 'Sudden Poop Explosion Disease.',	'w529' => 'The boners of the elderly.',	'w530' => 'The economy.',	'w531' => 'The Fanta girls.',	'w532' => 'The Gulags.',	'w533' => 'The harsh light of day.',	'w534' => 'The hiccups.',	'w535' => 'The shambling corpse of Larry King.',	'w536' => 'The four arms of Vishnu.',	'w537' => 'Being a busy adult with many important things to do.',	'w538' => 'Tripping balls.',	'w539' => 'Words, words, words.',	'w540' => 'Zeus\'s sexual appetites.',);
    public static $black_card_vals = array(
'b1' => 'TSA guidelines now prohibit __________ on airplanes.',	'b2' => 'It\'s a pity that kids these days are all getting involved with __________.',	'b3' => 'In 1,000 years, when paper money is but a distant memory, __________ will be our currency.',	'b4' => 'What is Batman\'s guilty pleasure?',	'b5' => 'Next from J.K. Rowling: Harry Potter and the Chamber of __________.',	'b6' => 'What did I bring back from Mexico?',	'b7' => '__________? There\'s an app for that.',	'b8' => '__________. Betcha can\'t have just one!',	'b9' => 'What\'s my anti-drug?',	'b10' => 'While the United States raced the Soviet Union to the moon, the Mexican government funneled millions of pesos into research on __________.',	'b11' => 'In the new Disney Channel Original Movie, Hannah Montana struggles with __________ for the first time. ',	'b12' => 'What\'s my secret power?',	'b13' => 'What\'s the new fad diet?',	'b14' => 'What did Vin Diesel eat for dinner?',	'b15' => 'When Pharaoh remained unmoved, Moses called down a Plague of __________.',	'b16' => 'How am I maintaining my relationship status?',	'b17' => 'What\'s the crustiest?',	'b18' => 'In L.A. County Jail, word is you can trade 200 cigarettes for __________.',	'b19' => 'After the earthquake, Sean Penn brought __________ to the people of Haiti.',	'b20' => 'Instead of coal, Santa now gives the bad children __________.',	'b21' => 'Life for American Indians was forever changed when the White Man introduced them to __________.',	'b22' => 'What\'s Teach for America using to inspire inner city students to succeed?',	'b23' => 'Maybe she\'s born with it. Maybe it\'s __________.',	'b24' => 'In Michael Jackson\'s final moments, he thought about __________.',	'b25' => 'White people like __________.',	'b26' => 'Why do I hurt all over?',	'b27' => 'A romantic, candlelit dinner would be incomplete without __________.',	'b28' => 'What will I bring back in time to convince people that I am a powerful wizard?',	'b29' => 'BILLY MAYS HERE FOR __________.',	'b30' => 'The class field trip was completely ruined by __________.',	'b31' => 'What\'s a girl\'s best friend?',	'b32' => 'Dear Abby, I\'m having some trouble with __________ and would like your advice.',	'b33' => 'When I am President of the United States, I will create the Department of __________.',	'b34' => 'What are my parents hiding from me?',	'b35' => 'What never fails to liven up the party?',	'b36' => 'What gets better with age?',	'b37' => '__________: Good to the last drop.',	'b38' => 'I got 99 problems but __________ ain\'t one.',	'b39' => '__________. It\'s a trap!',	'b40' => 'MTV\'s new reality show features eight washed-up celebrities living with __________.',	'b41' => 'What would grandma find disturbing, yet oddly charming?',	'b42' => 'What\'s the most emo?',	'b43' => 'During sex, I like to think about __________.',	'b44' => 'What ended my last relationship?',	'b45' => 'What\'s that sound?',	'b46' => '__________. That\'s how I want to die.',	'b47' => 'Why am I sticky?',	'b48' => 'What\'s the next Happy Meal toy?',	'b49' => 'What\'s there a ton of in heaven?',	'b50' => 'I do not know with what weapons World War III will be fought, but World War IV will be fought with __________.',	'b51' => 'What will always get you laid?',	'b52' => '__________: Kid-tested, mother-approved.',	'b53' => 'Why can\'t I sleep at night?',	'b54' => 'What\'s that smell?',	'b55' => 'What helps Obama unwind?',	'b56' => 'This is the way the world ends / This is the way the world ends / Not with a bang but with __________.',	'b57' => 'Coming to Broadway this season, __________: The Musical.',	'b58' => 'Anthropologists have recently discovered a primitive tribe that worships __________.',	'b59' => 'But before I kill you, Mr. Bond, I must show you __________.',	'b60' => 'Studies show that lab rats navigate mazes 50% faster after being exposed to __________.',	'b61' => 'Next on ESPN2: The World Series of __________.',	'b62' => 'When I am a billionaire, I shall erect a 50-foot statue to commemorate __________.',	'b63' => 'In an attempt to reach a wider audience, the Smithsonian Museum of Natural History has opened an interactive exhibit on __________.',	'b64' => 'War! What is it good for?',	'b65' => 'What gives me uncontrollable gas?',	'b66' => 'What do old people smell like?',	'b67' => 'What am I giving up for Lent?',	'b68' => 'Alternative medicine is now embracing the curative powers of __________.',	'b69' => 'What did the US airdrop to the children of Afghanistan?',	'b70' => 'What does Dick Cheney prefer?',	'b71' => 'During Picasso\'s often-overlooked Brown Period, he produced hundreds of paintings of __________.',	'b72' => 'What don\'t you want to find in your Chinese food?',	'b73' => 'I drink to forget __________.',	'b74' => '__________. High five, bro.',	'b75' => 'He who controls __________ controls the world.',	'b76' => 'The CIA now interrogates enemy agents by repeatedly subjecting them to __________.',	'b77' => 'In Rome, there are whisperings that the Vatican has a secret room devoted to __________.',	'b78' => 'Science will never explain the origin of __________.',	'b79' => 'When all else fails, I can always masturbate to __________.',	'b80' => 'I learned the hard way that you can\'t cheer up a grieving friend with __________.',	'b81' => 'In its new tourism campaign, Detroit proudly proclaims that it has finally eliminated __________.',	'b82' => 'The socialist governments of Scandinavia have declared that access to __________ is a basic human right.',	'b83' => 'In his new self-produced album, Kanye West raps over the sounds of __________.',	'b84' => 'What\'s the gift that keeps on giving?',	'b85' => 'This season on Man vs. Wild, Bear Grylls must survive in the depths of the Amazon with only __________ and his wits. ',	'b86' => 'When I pooped, what came out of my butt?',	'b87' => 'In the distant future, historians will agree that __________ marked the beginning of America\'s decline.',	'b88' => 'What has been making life difficult at the nudist colony?',	'b89' => 'And I would have gotten away with it, too, if it hadn\'t been for __________.',	'b90' => 'What brought the orgy to a grinding halt?',	'b91' => '__________. High five, bro.',	'b92' => 'TSA guidelines now prohibit __________ on airplanes.',	'b93' => 'It\'s a pity that kids these days are all getting involved with __________.',	'b94' => 'In 1,000 years, when paper money is but a distant memory, __________ will be our currency.',	'b95' => 'Major League Baseball has banned __________ for giving players an unfair advantage.',	'b96' => 'What is Batman\'s guilty pleasure?',	'b97' => 'Next from J.K. Rowling: Harry Potter and the Chamber of __________.',	'b98' => 'I\'m sorry, Professor, but I couldn\'t complete my homework because of __________.',	'b99' => 'What did I bring back from Mexico?',	'b100' => '__________? There\'s an app for that.',	'b101' => 'Betcha can\'t have just one!',	'b102' => 'What\'s my anti-drug?',	'b103' => 'While the United States raced the Soviet Union to the moon, the Mexican government funneled millions of pesos into research on __________.',	'b104' => 'In the new Disney Channel Original Movie, Hannah Montana struggles with __________ for the first time.',	'b105' => 'What\'s my secret power?',	'b106' => 'What\'s the new fad diet?',	'b107' => 'What did Vin Diesel eat for dinner?',	'b108' => 'When Pharaoh remained unmoved, Moses called down a Plague of __________.',	'b109' => 'How am I maintaining my relationship status?',	'b110' => 'What\'s the crustiest?',	'b111' => 'When I\'m in prison, I\'ll have __________ smuggled in.',	'b112' => 'After Hurricane Katrina, Sean Penn brought __________ to the people of New Orleans.',	'b113' => 'Instead of coal, Santa now gives the bad children __________.',	'b114' => 'Life was difficult for cavemen before __________.',	'b115' => 'What\'s Teach for America using to inspire inner city students to succeed?',	'b116' => 'Who stole the cookies from the cookie jar?',	'b117' => 'In Michael Jackson\'s final moments, he thought about __________.',	'b118' => 'White people like __________.',	'b119' => 'Why do I hurt all over?',	'b120' => 'A romantic candlelit dinner would be incomplete without __________.',	'b121' => 'What will I bring back in time to convince people that I am a powerful wizard?',	'b122' => 'BILLY MAYS HERE FOR __________.',	'b123' => 'The class field trip was completely ruined by __________.',	'b124' => 'What\'s a girl\'s best friend?',	'b125' => 'I wish I hadn\'t lost the instruction manual for __________.',	'b126' => 'When I am President of the United States, I will create the Department of __________.',	'b127' => 'What are my parents hiding from me?',	'b128' => 'What never fails to liven up the party?',	'b129' => 'What gets better with age?',	'b130' => '__________: good to the last drop.',	'b131' => 'I got 99 problems but __________ ain\'t one.',	'b132' => 'It\'s a trap!',	'b133' => 'MTV\'s new reality show features eight washed-up celebrities living with __________.',	'b134' => 'What would grandma find disturbing, yet oddly charming?',	'b135' => 'What\'s the most emo?',	'b136' => 'During sex, I like to think about __________.',	'b137' => 'What ended my last relationship?',	'b138' => 'What\'s that sound?',	'b139' => '__________. That\'s how I want to die.',	'b140' => 'Why am I sticky?',	'b141' => 'What\'s the next Happy Meal® toy?',	'b142' => 'What\'s there a ton of in heaven?',	'b143' => 'I do not know with what weapons World War III will be fought, but World War IV will be fought with __________.',	'b144' => 'What will always get you laid?',	'b145' => '__________: kid tested, mother approved.',	'b146' => 'Why can\'t I sleep at night?',	'b147' => 'What\'s that smell?',	'b148' => 'What helps Obama unwind?',	'b149' => 'This is the way the world ends \\ This is the way the world ends \\ Not with a bang but with __________.',	'b150' => 'Coming to Broadway this season, __________: The Musical.',	'b151' => 'Anthropologists have recently discovered a primitive tribe that worships __________.',	'b152' => 'But before I kill you, Mr. Bond, I must show you __________.',	'b153' => 'Studies show that lab rats navigate mazes 50% faster after being exposed to __________.',	'b154' => 'Due to a PR fiasco, Walmart no longer offers __________.',	'b155' => 'When I am a billionaire, I shall erect a 50-foot statue to commemorate __________.',	'b156' => 'In an attempt to reach a wider audience, the Smithsonian Museum of Natural History has opened an interactive exhibit on __________.',	'b157' => 'War! What is it good for?',	'b158' => 'What gives me uncontrollable gas?',	'b159' => 'What do old people smell like?',	'b160' => 'Sorry everyone, I just __________.',	'b161' => 'Alternative medicine is now embracing the curative powers of __________.',	'b162' => 'The U.S. has begun airdropping __________ to the children of Afghanistan.',	'b163' => 'What does Dick Cheney prefer?',	'b164' => 'During Picasso\'s often-overlooked Brown Period, he produced hundreds of paintings of __________.',	'b165' => 'What don\'t you want to find in your Chinese food?',	'b166' => 'I drink to forget __________.',	'b167' => 'What\'s the next superhero/sidekick duo?',	'b168' => 'Major League Baseball has banned __________ for giving players an unfair advantage.',	'b169' => 'I\'m sorry, Professor, but I couldn\'t complete my homework because of __________.',
    );

    public function start() {
		//header('Content-Type: application/json');
        
        $msgs = array();

        //Extract and immediately sanitize GET variables.
        $args = $_GET;
        foreach($args as $a => $v) {
            $a = Logic::processUGC($a);
            $v = Logic::processUGC($v);

            $args[$a] = $v;
        }
        $this->validateInput( $args );
        $this->checkValidGamestateForArgs( $args );
        
        $temp = $this->createPlayersFromArgs( $args, $msgs );
		$players = $temp['players'];
		$msgs = array_merge($msgs, $temp['msgs']);
		if (empty($players)) {
			throw new Exception('No players set for this game - please set some.');
		}
		$game = new Game();

        $players = $this->initPlayers($game, $players);
        foreach($players as $p) {
            $this->givePlayerHand( $game, $p, static::$hand_size);
        }
//print_r($players);
        $this->setPlayerToJudge($game, $players[0]);
        //$this->startRound();
    }
    
    //Make sure that everything that should be there is there, and strip out everything else.
    public function validateInput( $args ) {
        return;
    }
    
    //Determine whether this is an existing game or initiating a new game ('state=newgame')
    //Return a public 
    public function checkValidGamestateForArgs( $args ) {
		if (!isset($args['game_state']) || $args['game_state'] != 'new_game') {
            return;
        }
    }

    public function setPlayerToJudge( $game, $player ) {
		$current_judge = $game->current_judge;
		if ($current_judge != NULL) {
			$current_judge->is_current_judge = FALSE;
		}
		$game->current_judge = $player;
		$player->is_current_judge = TRUE;
print_r($game);
        return TRUE;
    }

    public function createPlayersFromArgs( $args, $msgs ) {
        $iter = 1;
        foreach (range(1, 7) as $i ) {
            if (isset($args['player' . $i]) && isset($args['email' . $i])) {
                $players[ $args['email' . $i] ] = $args['player' . $i];
            } elseif (!isset($args['player' . $i]) && !isset($args['email' . $i])) {
                continue;
            } else {
                $msgs[] = 'Player #'. $i .' missing either name or email.';
            }
        }
        return array('msgs' => $msgs, 'players' => $players);
    }

    public function initPlayers($game, $players) {
        if (sizeof($players) != sizeof(array_unique($players))) {
            throw new Exception( array(
                'msg' => 'All player names must be unique.',
                'direct' => 'start'
            ));
        }
		//Shuffle the player order, so we can reliably/fairly start with [0] going first.
		$game->shuffle_assoc($players);
		foreach ($players as $email_address => $display_name) {
			if (!isset($email_address) || !isset($display_name)) {
				continue;
			}
//echo 'Instantiating new player with args | '. $email_address.' | '.$display_name.' | on '.__line__."\n";
			$player = new Player( $email_address, $display_name );

			$game->addPlayerToGame( $player );
		}

		$all_players = $game->getAllPlayersFromGame();

        //TODO: Email a link out to all players, with their UIDs / game invites
        //...And until we do that, we'll just let them get their hash themselves.

        return $all_players;
    }

    public function givePlayerCard( $game, $player, $number_of_cards = NULL ) {
		if ($number_of_cards === NULL) {
			$number_of_cards = static::$hand_size;
		}
        $game_id = $game->getGameId();
        $white_deck = $game->getWhiteDeck( $game_id );
        if (sizeof($white_deck) < 1) {
            $discard = $game->getWhiteDiscard( $game_id );
            $white_deck = $game->shuffleDiscardIntoDeck( $game_id, $discard );
        }
        $new_card_id = array_rand( $white_deck );
        $player->addToHand( $player, $white_deck, $new_card_id );
        $current_hand = $player->getHand();

        return $current_hand;
    }

    public function givePlayerHand( $game, $player, $number_of_cards = 7 ) {
		$current_hand = array();
        while (count($current_hand) < $number_of_cards) {
            $current_hand = $this->givePlayerCard( $game, $player );
        }
        return $current_hand;
    }

}

class Logic {
    public static $rand_chars = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public static $length_of_rand_chars = 62;
    public static $suggested_hash_types = array('sha256', 'sha384', 'sha512', 'ripemd256', 'ripemd320', 'whirlpool', 'snefru', 'gost');
    public static $default_hash_type = 'whirlpool';

    public function createPublicGameId() {
        $game_id = '';

        for ($i = 0; $i < 10; $i++) {
            $game_id .= static::$rand_chars[mt_rand(1, static::$length_of_rand_chars)];
        }
        return $game_id;
    }

    //Strip out all characters that are not `alphanum/_/-/./@` . This is *stricter* than it needs to be
    //(http://haacked.com/archive/2007/08/21/i-knew-how-to-validate-an-email-address-until-i.aspx),
    //but tough.
    public function processUGC( $haystack ) {
        $needle = '[^A-Za-z0-9_-@.]';
        $replacement = '';

        $input = preg_replace( $needle, $replacement, $haystack );

        return $input;
    }
}

class Player {
	public $email;
	public $display_name;
	public $hash;
	public $hand = array();
	public $points = 0;
	public $is_current_judge = FALSE;

	function __construct( $email_address, $display_name ) {
		$this->email = $email_address;
		$this->display_name = $display_name;
		$this->hash = $this->setPlayerHash($email_address, $display_name);
	}

	public function setPlayerHash( $email_address, $display_name ) {
		$key = $email_address . $display_name . Init::$salt;
		$hash = hash( 'sha512', $key );

		$hash = substr($hash, 0, 10);

		return $hash;
	}

	public function getHand() {
		return $this->hand;
	}

	public function addToHand ( $player, $deck, $card_id ) {
		$player->hand[$card_id] = $deck[$card_id];
		return TRUE;
    }
}

class Transport {
    public function receiveHand( $player ) {
        //$this->
    }
}
//echo 'zoink';
$game = new Init;
$done = $game->start();

$game_id = 321123;
$player_id = 'f00ba5ba5';