<?php
/**
 * File containing the DisqusType class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v2
 * @author Jérôme Vieilledent
 */

/**
 * Description of DisqusType
 */
class DisqusCommentsType extends eZDataType
{
    const  DATA_TYPE_STRING = 'disquscomments';

    const CLASSATTRIBUTE_COMMENTS_ACTIVATED_DEFAULT_FIELD = 'data_int1',
          CLASSATTRIBUTE_COMMENTS_ACTIVATED_DEFAULT = 0;

    const COMMENTS_ENABLED_VARIABLE = '_disquscomments_enabled_';

    public function __construct()
    {
        parent::eZDataType(
            self::DATA_TYPE_STRING,
            ezpi18n::tr( 'ezdisqus/datatype', 'Disqus comments' )
        );
    }

    // --------------------------------------
    // Methods concerning the CLASS attribute
    // --------------------------------------

    /**
     * Sets default values for a new class attribute.
     *
     * @param eZContentClassAttribute $classAttribute
     * @return void
     */
    public function initializeClassAttribute( $classAttribute )
    {
    }

    /**
     * Validates the input from the class definition form concerning this attribute.
     *
     * @param eZHTTPTool $http
     * @param string $base Seems to be always 'ContentClassAttribute'.
     * @param eZContentClassAttribute $classAttribute
     * @return int eZInputValidator::STATE_ACCEPTED|eZInputValidator::STATE_INVALID|eZInputValidator::STATE_INTERMEDIATE
     */
    public function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fixes up the data that has been posted with the class edit form
     * This method is called only if validation method (self::validateClassAttributeHTTPInput()) returned eZInputValidator::STATE_INTERMEDIATE
     *
     * @param eZHTTPTool $http
     * @param string $base POST variable name prefix (Always "ContentObjectAttribute")
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @see eZDataType::fixupClassAttributeHTTPInput()
     */
    public function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
    }

    /**
     * Handles the input specific for one attribute from the class edit interface.
     *
     * @param eZHTTPTool $http
     * @param string $base Seems to be always 'ContentClassAttribute'.
     * @param eZContentClassAttribute $classAttribute
     * @return void
     */
    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        // Comments default activation
        $activationFieldName = $base . self::COMMENTS_ENABLED_VARIABLE . $classAttribute->attribute( 'id' );
        if( $http->hasPostVariable( $activationFieldName ) )
        {
            $classAttribute->setAttribute( self::CLASSATTRIBUTE_COMMENTS_ACTIVATED_DEFAULT_FIELD, 1 );
        }
    }

    /**
     * Returns the content for the class attribute
     * Result is an associative array :
     * 		- default_activated (bool)
     * 		- comments_by_location (bool)
     *
     * @param eZContentClassAttribute $classAttribute
     * @return array
     * @see eZDataType::classAttributeContent()
     */
    public function classAttributeContent( $classAttribute )
    {
        return array(
            'default_activated'     => (bool)$classAttribute->attribute( self::CLASSATTRIBUTE_COMMENTS_ACTIVATED_DEFAULT_FIELD ),
        );
    }

    // --------------------------------------
    // Methods concerning the OBJECT attribute
    // --------------------------------------

    /**
     * Initializes object attribute before displaying edit template
     * Can be useful to define default values. Default values can be defined in class attributes
     *
     * @param eZContentObjectAttribute $contentObjectAttribute Object attribute for the new version
     * @param int $currentVersion Version number. NULL if this is the first version
     * @param eZContentObjectAttribute $originalContentObjectAttribute Object attribute of the previous version
     * @see eZDataType::initializeObjectAttribute()
     */
    public function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        // Sets default values on first version
        if( $currentVersion === null )
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $contentObjectAttribute->setAttribute(
                'data_int',
                $contentClassAttribute->attribute( self::CLASSATTRIBUTE_COMMENTS_ACTIVATED_DEFAULT_FIELD )
            );
        }
    }

    /**
     * Validates input on content object level
     *
     * @param eZHTTPTool $http
     * @param string $base POST variable name prefix (Always "ContentObjectAttribute")
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return eZInputValidator::STATE_ACCEPTED|eZInputValidator::STATE_INVALID|eZInputValidator::STATE_INTERMEDIATE
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Fixes up the data that has been posted with the object edit form
     * This method is called only if validation method (self::validateObjectAttributeHTTPInput()) returned eZInputValidator::STATE_INTERMEDIATE
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $objectAttribute
     * @see eZDataType::fixupObjectAttributeHTTPInput()
     */
    public function fixupObjectAttributeHTTPInput( $http, $base, $objectAttribute )
    {
    }

    /**
     * Fetches all variables from the object and handles them
     * Data store can be done here
     *
     * @param eZHTTPTool $http
     * @param string $base POST variable name prefix (Always "ContentObjectAttribute")
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return true if fetching of object attributes is successful, false if not
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $fieldName = $base . self::COMMENTS_ENABLED_VARIABLE . $contentObjectAttribute->attribute( 'id' );
        if( $http->hasPostVariable( $fieldName ) )
        {
            $contentObjectAttribute->setAttribute( 'data_int', 1 );
        }

        return true;
    }

    /**
     * Checks if current content object attribute has content
     * Returns true if it has content
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return bool
     * @see eZDataType::hasObjectAttributeContent()
     * @todo Check if there are comments through Disqus API
     */
    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return true;
    }

    /**
     * Returns the content.
     * Result is an associative array :
     *      - comments_enabled : Boolean indicating if comments are enabled or not for content
     * 	    - shortname : Disqus short name
     * 	    - identifier : Content identifier for Disqus
     *      - title : The content object name
     *      - url : URL for main node
     *
     * @param eZContentObjectAttribute
     * @return array
     */
    public function objectAttributeContent( $contentObjectAttribute )
    {
        $contentObject = $contentObjectAttribute->object();
        return array(
            'comments_enabled' => (bool)$contentObjectAttribute->attribute( 'data_int' ),
            'shortname'        => eZINI::instance( 'disqus.ini' )->variable( 'Base', 'DisqusShortname' ),
            'identifier'       => $contentObjectAttribute->attribute( 'contentobject_id' ),
            'title'            => $contentObject->name(),
            'url'              => $contentObject->mainNode()->urlAlias()
        );
    }

    /**
     * Returns the value as it will be shown if this attribute is used in the object name pattern.
     *
     * @param eZContentObjectAttribute
     * @name string
     * @return string
     */
    public function title( $contentObjectAttribute, $name = null )
    {
        return ezpI18n::tr( 'ezdisqus/datatype', 'Disqus comments' );
    }

    /**
     * @return true if the datatype can be indexed
     */
    public function isIndexable()
    {
        return false;
    }

    /**
     * Initializes the object attribute from a string representation
     *
     * @param eZContentObjectAttribute
     * @param string
     * @see eZDataType::fromString()
     */
    public function fromString( $objectAttribute, $string )
    {
        $objectAttribute->setAttribute( 'data_int', (int)$string );
    }

    /**
     * Returns the string representation of the object attribute
     *
     * @param eZContentObjectAttribute
     * @see eZDataType::toString()
     * @return string
     */
    public function toString( $objectAttribute )
    {
        return (string)$objectAttribute->attribute( 'data_int' );
    }

    /**
     * Returns the sort type. Can be 'string', 'int' ('float' is not supported) or false if sorting is not supported
     *
     * @see eZDataType::sortKeyType()
     */
    public function sortKeyType()
    {
        return 'int';
    }

    /**
     * Returns the sort key, for sorting at the attribute level
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return string
     * @see eZDataType::sortKey()
     */
    public function sortKey( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_int' );
    }
}

eZDataType::register( DisqusCommentsType::DATA_TYPE_STRING, 'DisqusCommentsType' );
