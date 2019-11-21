<?php

namespace App\Policies;

use App\User;
use App\UserRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any user requests.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
      return true;
    }

    /**
     * Determine whether the user can view the user request.
     *
     * @param  \App\User  $user
     * @param  \App\UserRequest  $userRequest
     * @return mixed
     */
    public function view(User $user, UserRequest $userRequest)
    {
        //
    }

    /**
     * Determine whether the user can create user requests.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the user request.
     *
     * @param  \App\User  $user
     * @param  \App\UserRequest  $userRequest
     * @return mixed
     */
    public function update(User $user, UserRequest $userRequest)
    {
      return ($userRequest->other_user_id == $user->id);
    }

    /**
     * Determine whether the user can delete the user request.
     *
     * @param  \App\User  $user
     * @param  \App\UserRequest  $userRequest
     * @return mixed
     */
    public function delete(User $user, UserRequest $userRequest)
    {
      return ($userRequest->user_id == $user->id) || ($userRequest->other_user_id == $user->id);
    }

    /**
     * Determine whether the user can restore the user request.
     *
     * @param  \App\User  $user
     * @param  \App\UserRequest  $userRequest
     * @return mixed
     */
    public function restore(User $user, UserRequest $userRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the user request.
     *
     * @param  \App\User  $user
     * @param  \App\UserRequest  $userRequest
     * @return mixed
     */
    public function forceDelete(User $user, UserRequest $userRequest)
    {
        //
    }
}
