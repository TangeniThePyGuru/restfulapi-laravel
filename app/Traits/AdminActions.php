<?php
	/**
	 * Created by PhpStorm.
	 * User: MT_IT
	 * Date: 6/4/2018
	 * Time: 11:41 PM
	 */

	namespace App\Traits;


	use App\User;

	trait AdminActions
	{
		/**
		 * before execution check that the user type
		 * @param User $user
		 * @param $ability
		 * @return bool
		 */
		public function before(User $user, $ability)
		{
			if ($user->isAdmin()){
				return true;
			}

			return false;
		}
	}