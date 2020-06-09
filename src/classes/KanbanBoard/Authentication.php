<?php declare(strict_types=1);

namespace App\KanbanBoard;

use App\KanbanBoard\Interfaces\AuthenticationInterface;
use App\Utilities;
use GuzzleHttp\Exception\ClientException;

class Authentication implements AuthenticationInterface {

    /**
     * GitHub Api Client Id
     * @var string $clientId
     */
    private string $clientId;

    /**
     * GitHub Api Client Secret
     * @var string $clientSecret
     */
	private string $clientSecret;

    /**
     * Authentication constructor.
     * @throws \App\Exceptions\EnvVariableNotFoundException
     */
	public function __construct() {
	    session_start();
		$this->clientId = Utilities::env('GH_CLIENT_ID');
		$this->clientSecret = Utilities::env('GH_CLIENT_SECRET');
	}

    /**
     * Destroys the current session
     * @return void
     */
	public function logout(): void {
	    session_destroy();
	}

    /**
     * oAuth2 Authentication implementation
     *
     * @see https://developer.github.com/v3/#authentication
     * @throws \GuzzleHttp\Exception\ClientException
     * @return string GitHub's access_token
     */
	public function login(): string {
		if ( Utilities::hasValue($_SESSION, 'gh-token') ) {
		    return $_SESSION['gh-token'];
        }

		if(Utilities::hasValue($_GET, 'code')
			&& Utilities::hasValue($_GET, 'state')
               && $_SESSION['state'] == $_GET['state']
			&& $_SESSION['redirected'])
		{
			$_SESSION['redirected'] = false;
			try {
                $_SESSION['gh-token'] = $token = $this->_returnsFromGithub($_GET['code']);
                unset($_SESSION['state'], $_SESSION['redirected']);
                header("Location:/");
                exit();
            } catch (ClientException $exception) {
			    session_destroy();
			    throw $exception;
            }
		}
		else
		{
			$_SESSION['redirected'] = true;
			$this->_redirectToGithub();
		}
	}

    /**
     * Authorizes client for GitHub authentication
     *
     * @see https://developer.github.com/v3/#authentication
     * @return void
     */
	private function _redirectToGithub(): void {
        $_SESSION['state'] = $state = Utilities::randomString();

        $query = http_build_query([
            'client_id' => $this->clientId,
            'scope' => 'repo',
            'state' => $state,
        ]);

		header('Location: https://github.com/login/oauth/authorize?' . $query);
		exit();
	}

    /**
     * Requests access_token from GitHub
     *
     * @see https://developer.github.com/v3/#authentication
     * @param string $code Authorization code from GitHub
     * @return string access_token
     */
	private function _returnsFromGithub(string $code): string {
        $response = (new \GuzzleHttp\Client)->post('https://github.com/login/oauth/access_token', [
            'form_params' => [
                'code' => $code,
                'state' => $_SESSION['state'],
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            ],
        ]);

        parse_str((string) $response->getBody(), $result);

        return $result['access_token'];
	}

}
