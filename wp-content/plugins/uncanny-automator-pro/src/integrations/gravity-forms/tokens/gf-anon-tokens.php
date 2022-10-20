<?php


namespace Uncanny_Automator_Pro;


use Uncanny_Automator\Gf_Tokens;

class Gf_Anon_Tokens extends Gf_Tokens {

	public function __construct() {
		//*************************************************************//
		// See this filter generator AT automator-get-data.php
		// in function recipe_trigger_tokens()
		//*************************************************************//
		//add_filter( 'automator_maybe_trigger_gf_tokens', [ $this, 'gf_general_tokens' ], 20, 2 );
		//add_filter( 'automator_maybe_trigger_gf_anongfforms_tokens', [ $this, 'gf_possible_tokens' ], 20, 2 );
	}

}
