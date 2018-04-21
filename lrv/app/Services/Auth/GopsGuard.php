<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use phpDocumentor\Reflection\Types\Array_;
use Illuminate\Auth\AuthenticationException;

class GopsGuard implements Guard
{
  protected $request;
  protected $provider;
  protected $user;

  /**
   * Create a new authentication guard.
   *
   * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
   * @param  \Illuminate\Http\Request  $request
   * @return void
   */
  public function __construct(UserProvider $provider, Request $request)
  {
    $this->request = $request;
    $this->provider = $provider;
    $this->user = NULL;
  }

  /**
   * Determine if the current user is authenticated.
   *
   * @return bool
   */
  public function check()
  {
    return ! is_null($this->user());
  }

  /**
   * Determine if the current user is a guest.
   *
   * @return bool
   */
  public function guest()
  {
    return ! $this->check();
  }

  /**
   * Get the currently authenticated user.
   *
   * @return \Illuminate\Contracts\Auth\Authenticatable|null
   */
  public function user()
  {
    if (! is_null($this->user)) {
      return $this->user;
    }

    session_start();
    if(isset($_SESSION['userid'])) {
      $userid = $_SESSION['userid'];
      $user = $this->provider->retrieveById($userid);

      if($user) {
        $this->setUser($user);
        return $user;
      }
    }
  }

  /**
   * Get the ID for the currently authenticated user.
   *
   * @return string|null
  */
  public function id()
  {
    if ($user = $this->user()) {
      return $this->user()->getAuthIdentifier();
    }
  }

  /**
   * Validate a user's credentials.
   *
   * @return bool
   */
  public function validate(Array $credentials=[])
  {
    if (empty($credentials['usercode']) || empty($credentials['password'])) {
      if (!$credentials=$this->getCredentialsFromParams()) {
        return false;
      }
    }

    $user = $this->provider->retrieveByCredentials($credentials);

    if (! is_null($user) && $this->provider->validateCredentials($user, $credentials)) {
      $this->setUser($user);

      return true;
    } else {
      return false;
    }
  }

  /**
   * Set the current user.
   *
   * @param  Array $user User info
   * @return void
   */
  public function setUser(Authenticatable $user)
  {
    $this->user = $user;
  }

  // =========== our methods ===========

  public function authenticate() {
    if(!$this->check()) {
      throw new AuthenticationException('Unauthenticated.', [ $this ]);
    }
  }

  /**
   * Get the credentials from the current request params
   *
   * @return string
   */
  public function getCredentialsFromParams()
  {
    $user = $this->request->input('user');
    $pcode = $this->request->input('pcode');

    $credentials = (!empty($user) && !empty($pcode)) ? ['usercode' => $user, 'password' => $pcode] : NULL;
    return $credentials;
  }
}
