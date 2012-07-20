<?php
/**
 * File containing the Repository class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Core\REST\Client;

use \eZ\Publish\API\Repository\Values\ValueObject;
use \eZ\Publish\API\Repository\Values\Content\Content;
use \eZ\Publish\API\Repository\Values\Content\ContentInfo;
use \eZ\Publish\API\Repository\Values\Content\Location;
use \eZ\Publish\API\Repository\Values\Content\VersionInfo;
use \eZ\Publish\API\Repository\Values\User\User;
use \eZ\Publish\API\Repository\Values\User\Limitation;

use \eZ\Publish\Core\REST\Common;

/**
 * REST Client Repository
 *
 * @see \eZ\Publish\API\Repository\Repository
 */
class Repository implements \eZ\Publish\API\Repository\Repository
{
    /**
     * @var integer
     */
    private $version;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\User
     */
    private $currentUser;

    /**
     * @var \eZ\Publish\Core\REST\Client\SectionService
     */
    private $sectionService;

    /**
     * @var \eZ\Publish\Core\REST\Client\LanguageService
     */
    private $languageService;

    /**
     * @var \eZ\Publish\Core\REST\Client\UserService
     */
    private $userService;

    /**
     * @var \eZ\Publish\Core\REST\Client\RoleService
     */
    private $roleService;

    /**
     * @var \eZ\Publish\Core\REST\Client\UrlAliasService
     */
    private $urlAliasService;

    /**
     * @var \eZ\Publish\Core\REST\Client\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\Core\REST\Client\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \eZ\Publish\Core\REST\Client\TrashService
     */
    private $trashService;

    /**
     * @var \eZ\Publish\Core\REST\Client\LocationService
     */
    private $locationService;

    /**
     * @var \eZ\Publish\Core\REST\Client\IOService
     */
    private $ioService;

    /**
     * Client
     *
     * @var \eZ\Publish\Core\REST\Client\HttpClient
     */
    private $client;

    /**
     * Input parsing dispatcher
     *
     * @var \eZ\Publish\Core\REST\Common\Input\Dispatcher
     */
    private $inputDispatcher;

    /**
     * @var \eZ\Publish\Core\REST\Common\Output\Visitor
     */
    private $outputVisitor;

    /**
     * @var \eZ\Publish\Core\REST\Common\UrlHandler
     */
    private $urlHandler;

    /**
     * Instantiates the REST Client repository.
     *
     * @param \eZ\Publish\Core\REST\Client\HttpClient $client
     * @param \eZ\Publish\Core\REST\Common\Input\Dispatcher $inputDispatcher
     * @param \eZ\Publish\Core\REST\Common\Output\Visitor $outputVisitor
     * @param \eZ\Publish\Core\REST\Common\UrlHandler $urlHandler
     */
    public function __construct( HttpClient $client, Common\Input\Dispatcher $inputDispatcher, Common\Output\Visitor $outputVisitor, Common\UrlHandler $urlHandler )
    {
        $this->client          = $client;
        $this->inputDispatcher = $inputDispatcher;
        $this->outputVisitor   = $outputVisitor;
        $this->urlHandler      = $urlHandler;
    }

    /**
     * Get current user
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    public function getCurrentUser()
    {
        return null;
    }

    /**
     * Sets the current user to the user with the given user id
     *
     * @param \eZ\Publish\API\Repository\Values\User\User $user
     * @return void
     */
    public function setCurrentUser( User $user )
    {
        throw new Exceptions\MethodNotAllowedException(
            'It is not allowed to set a current user in this implementation. Please use a corresponding authenticating HttpClient instead.'
        );
    }

    /**
     *
     *
     * @param string $module
     * @param string $function
     * @param \eZ\Publish\API\Repository\Values\User\User $user
     * @return boolean|\eZ\Publish\API\Repository\Values\User\Limitation[] if limitations are on this function an array of limitations is returned
     */
    public function hasAccess( $module, $function, User $user = null )
    {
        // @TODO: Implement
    }

    /**
     * Indicates if the current user is allowed to perform an action given by the function on the given
     * objects
     *
     * @param string $module
     * @param string $function
     * @param \eZ\Publish\API\Repository\Values\ValueObject $value
     * @param \eZ\Publish\API\Repository\Values\ValueObject $target
     * @return boolean
     */
    public function canUser( $module, $function, ValueObject $value, ValueObject $target = null )
    {
        // @TODO: Implement
    }

    /**
     * Get Content Service
     *
     * Get service object to perform operations on Content objects and it's aggregate members.
     *
     * @return \eZ\Publish\API\Repository\ContentService
     */
    public function getContentService()
    {
        if ( null === $this->contentService )
        {
            $this->contentService = new ContentService(
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->contentService;
    }

    /**
     * Get Content Language Service
     *
     * Get service object to perform operations on Content language objects
     *
     * @return \eZ\Publish\API\Repository\LanguageService
     */
    public function getContentLanguageService()
    {
        if ( null === $this->languageService )
        {
            $this->languageService = new LanguageService(
                $this->getContentService(),
                'eng-US',
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->languageService;
    }

    /**
     * Get Content Type Service
     *
     * Get service object to perform operations on Content Type objects and it's aggregate members.
     * ( Group, Field & FieldCategory )
     *
     * @return \eZ\Publish\API\Repository\ContentTypeService
     */
    public function getContentTypeService()
    {
        if ( null === $this->contentTypeService )
        {
            $this->contentTypeService = new ContentTypeService(
                $this->getContentService(),
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->contentTypeService;
    }

    /**
     * Get Content Location Service
     *
     * Get service object to perform operations on Location objects and subtrees
     *
     * @return \eZ\Publish\API\Repository\LocationService
     */
    public function getLocationService()
    {
        if ( null === $this->locationService )
        {
            $this->locationService = new LocationService(
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->locationService;
    }

    /**
     * Get Content Trash service
     *
     * Trash service allows to perform operations related to location trash
     * (trash/untrash, load/list from trash...)
     *
     * @return \eZ\Publish\API\Repository\TrashService
     */
    public function getTrashService()
    {
        if ( null === $this->trashService )
        {
            $this->trashService = new TrashService(
                $this->getLocationService(),
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->trashService;
    }

    /**
     * Get Content Section Service
     *
     * Get Section service that lets you manipulate section objects
     *
     * @return \eZ\Publish\API\Repository\SectionService
     */
    public function getSectionService()
    {
        if ( null === $this->sectionService )
        {
            $this->sectionService = new SectionService(
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->sectionService;
    }

    /**
     * Get Search Service
     *
     * Get search service that lets you find content objects
     *
     * @return \eZ\Publish\API\Repository\SearchService
     */
    public function getSearchService()
    {
        throw new \RuntimeException( '@TODO: Implememt.' );
    }

    /**
     * Get User Service
     *
     * Get service object to perform operations on Users and UserGroup
     *
     * @return \eZ\Publish\API\Repository\UserService
     */
    public function getUserService()
    {
        if ( null === $this->userService )
        {
            $this->userService = new UserService(
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->userService;
    }

    /**
     * Get IO Service
     *
     * Get service object to perform operations on binary files
     *
     * @return \eZ\Publish\API\Repository\IOService
     */
    public function getIOService()
    {
        if ( null === $this->ioService )
        {
            $this->ioService = new IOService(
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->ioService;
    }

    /**
     * Get RoleService
     *
     * @return \eZ\Publish\API\Repository\RoleService
     */
    public function getRoleService()
    {
        if ( null === $this->roleService )
        {
            $this->roleService = new RoleService(
                $this->getUserService(),
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->roleService;
    }

    /**
     * Get UrlAliasService
     *
     * @return \eZ\Publish\API\Repository\URLAliasService
     */
    public function getUrlAliasService()
    {
        if ( null === $this->urlAliasService )
        {
            $this->urlAliasService = new URLAliasService(
                $this->client,
                $this->inputDispatcher,
                $this->outputVisitor,
                $this->urlHandler
            );
        }
        return $this->urlAliasService;
    }

    /**
     * Get URLWildcardService
     *
     * @return \eZ\Publish\API\Repository\URLWildcardService
     */
    public function getURLWildcardService()
    {
        throw new \RuntimeException( '@TODO: Implement' );
    }

    /**
     * Get ObjectStateService
     *
     * @return \eZ\Publish\API\Repository\ObjectStateService
     */
    public function getObjectStateService()
    {
        throw new \RuntimeException( '@TODO: Implement' );
    }

    /**
     * Get FieldTypeService
     *
     * @return \eZ\Publish\API\Repository\FieldTypeService
     */
    public function getFieldTypeService()
    {
        throw new \RuntimeException( '@TODO: Implement.' );
    }

    /**
     * Begin transaction
     *
     * Begins an transaction, make sure you'll call commit or rollback when done,
     * otherwise work will be lost.
     */
    public function beginTransaction()
    {
        ++$this->transactionDepth;
    }

    /**
     * Commit transaction
     *
     * Commit transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function commit()
    {
        // @TODO: Implement / discuss
    }

    /**
     * Rollback transaction
     *
     * Rollback transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function rollback()
    {
        // @TODO: Implement / discuss
    }
}