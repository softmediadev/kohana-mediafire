<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_MediaFire {

	protected $mflib;
	protected $token;
	protected $config;
	protected $uploadkey;
	protected static $instance;
	
    /**
     * MediaFire::instance()
     * 
     * @param array $config
     * @access public
     */
    public static function instance( array $config = NULL )
    {
		if ( ! isset(self::$instance))
			self::$instance = new MediaFire($config);

		return self::$instance;
    }
    
    /**
     * Class constructor
     *
     * @access public
     */
    public function __construct( array $config = NULL )
    {
		$this->config = $config;
		
		if (empty($this->config))
			$this->config = Kohana::$config->load('mediafire')->as_array();
        
        $this->mflib = new mflib($this->config['appId'], $this->config['apiKey']);
        $this->mflib->email = $this->config['email'];
        $this->mflib->password = $this->config['password'];
		
		$this->token = $this->userGetSessionToken();
	}
	
	/**
     * Generates a 10-minute Access Session Token to be used in upcoming API
     * requests. Note: This call requires SSL
     *
     * @access public
     * @see userRenewSessionToken()
     * @return string|bool Returns a 10-minute Access Session Token on success, otherwise FALSE if an error occurred
     */
	public function userGetSessionToken()
	{
		return $this->mflib->userGetSessionToken($this->config['version']);
	}
	
	/**
     * Extends the life of the session token by another 10 minutes.
     *
     * If the session token is less than 5 minutes old, then it does not get
     * renewed and the same token is returned.
     *
     * If the token is more than 5 minutes old, then, depending on the
     * application configuration, the token gets extended or a new token is
     * generated and returned.
     *
     * @access public
     * @return string|bool Returns a new 10-minute Access Session Token on success, otherwise FALSE if an error occurred
     */
	public function userRenewSessionToken()
	{
		return $this->mflib->userRenewSessionToken($this->token);
	}
	
	/**
     * Generate link(s) for multiple people to edit files
     *
     * If email addresses are passed, contacts are created. If email addresses
     * are not passed, only edit links are returned. This API also returns the
     * daily collaboration link request count.
     *
     * @access public
     * @param array|string $quickkey (Optional) The quickkey or comma-separated list of quickkeys to be shared. If quickkeys are not passed, the daily sharing limit is returned
     * @param array|string $emails (Optional) A comma-separated list of email addresses to which an edit link will be sent. Can be an array instead
     * @param int $duration (Optional) The number of minutes the share link is valid. If an email address was not passed, the duration parameter is ignored, and the edit link is valid for 30 days
     * @param string $message (Optional) A short message to be sent with the notification emails. If email addresses were not passed, the message parameter is ignored
     * @param bool $public (Optional) If this parameter is set to TRUE, multiple people can use the same link to edit the document. The default is FALSE.
     * @return array|bool|string Returns an array contain the collaboration links and the number of daily collaboration link request count
     */
	public function fileCollaborate($quickkey = NULL, $emails = NULL, $duration = NULL, $message = NULL, $public = NULL, $emailNotification = NULL)
	{
		return $this->mflib->fileCollaborate($this->token, $quickkey, $emails, $duration, $message, $public, $emailNotification);
	}
	
	/**
     * Copy a file to a specified folder
     *
     * Any file can be copied whether it belongs to the session user or another
     * user. However, the target folder must be owned by the session caller.
     * Private files not owned by the session caller cannot be copied.
     *
     * @access public
     * @param array|string $quickkey The quickkey or a list of quickkeys that identify the files to be saved
     * @param string $folderKey (Optional) The key that identifies the destination folder. If omitted, the destination folder will be the root folder (Myfiles)
     * @return string
     */
	public function fileCopy($quickkey, $folderKey = NULL)
	{
		return $this->mflib->fileCopy($this->token, $quickkey, $folderKey);
	}
	
	/**
     * Deletes a single file or multiple files
     *
     * @access public
     * @param array|string $quickkey The quickkey that identifies the file. You can also specify multiple quickkeys separated by comma or just put them into an array
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function fileDelete($quickkey)
	{
		return $this->mflib->fileDelete($this->token, $quickkey);
	}
	
	/**
     * Returns details of a single file or multiple files
     *
     * @access public
     * @param array|string $quickkey The quickkey that identifies the file. You can also specify multiple quickkeys separated by comma or just put them into an array
     * @return array|bool Returns an array contains details of the file(s)
     */
	public function fileGetInfo($quickkey)
	{
		return $this->mflib->fileGetInfo($this->token, $quickkey);
	}
	
	/**
     * Return the view link, normal download link, and if possible the direct
     * download link of a file.
     *
     * The direct download link can only be generated for files uploaded by the 
     * MediaFire account owner himself/herself. If the link is not generated, 
     * an error message is returned explaining the reason
     *
     * @access public
     * @param array|string $quickkey The quickkey that identifies the file. You can also specify multiple quickkeys separated by comma or just put them into an array
     * @param string $linkType (Optional) Specify which link type is to be returned. If not passed, all link types are returned.
     * @return array|bool Returns an array contains links of the specified files, otherwise FALSE if an error occurred
     */
	public function fileGetLinks($quickkey, $linkType = NULL)
	{
		return $this->mflib->fileGetLinks($quickkey, $linkType, $this->token);
	}
	
	/**
     * Moves a single file or multiple files to a folder
     *
     * @access public
     * @param array|string $quickkey The quickkey that identifies the file. You can also specify multiple quickkeys separated by comma or just put them into an array
     * @param string $folderKey (Optional) The key that identifies the destination folder. If omitted, the destination folder will be the root folder (My files)
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function fileMove($quickkey, $folderKey = NULL)
	{
		return $this->mflib->fileMove($this->token, $quickkey, $folderKey);
	}
	
	/**
     * Create a one-time download link. This method can also be used to
     * configure/update an existing one-time download link
     *
     * @access public
     * @param array|string $quickkey The quickkey of the file to generate the one-time download link. If it is not passed, no link is generated, and the daily limit will be returned.
     * @param string $information (Optional) The updated information of the new/existing one-time download link
     * @return array|bool Returns an array on success, otherwise FALSE if an error occurred
     */
	public function fileOneTimeDownload($quickkey, $information)
	{
		return $this->mflib->fileOneTimeDownload($this->token, $quickkey, $information);
	}
	
	/**
     * Updates a file's information
     *
     * Example of usage :
     * <code>$mflib->fileUpdateInfo("8c4ff4fzufdbbip", array("description" => "a test file", "tags" => "test,file"));</code>
     *
     * @access public
     * @param string $quickkey The quickkey that identifies the file
     * @param array $information An associative array contains the updated file information
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function fileUpdateInfo($quickkey, $information)
	{
		return $this->mflib->fileUpdateInfo($this->token, $quickkey, $information);
	}
	
	/**
     * Updates a file's quickkey with another file's quickkey. Note: Only files
     * with the same file extension can be used with this operation
     *
     * @access public
     * @param string $fromQuickkey The quickkey of the file to be overriden. After this operation, this quickkey will be invalid
     * @param string $toQuickkey The new quickkey that will point to the file previously identified by fromQuickkey
     * @return array|bool Returns an array contains epoch and revision  number, otherwise FALSE if an error occurred
     */
	public function fileUpdate($fromQuickkey, $toQuickkey)
	{
		return $this->mflib->fileUpdate($this->token, $fromQuickkey, $toQuickkey);
	}
	
	/**
     * Updates a file's password
     *
     * @access public
     * @param string $quickkey The quickkey that identifies the file
     * @param string $password (Optional) The new password to be set. To remove the password protection, pass an empty string
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function fileUpdatePassword($quickkey, $password = NULL)
	{
		return $this->mflib->fileUpdatePassword($this->token, $quickkey, $password);
	}
	
	/**
     * Uploads a file
     *
     * The filetype can be explicitly specified by following the filename with
     * the type in the format ';type=mimetype'
     *
     * @access public
     * @param string $filename Path to the file to be uploaded
     * @param string $uploadKey (Optional) The quickkey of the destination folder. Default is 'myfiles', which means that the uploaded file will be stored in the root folder (My Files)
     * @param string $customName (Optional) Path to the file to be uploaded
     * @return bool|string Returns the upload key of the file, otherwise FALSE if an error occurred
     */
	public function fileUpload($filename, $uploadKey = 'myfiles', $customName = NULL)
	{
		$this->uploadkey = $this->mflib->fileUpload($this->token, $filename, $uploadKey, $customName);
		
		return $this->uploadkey;
	}
	
	/**
     * Check for the status of a current Upload
     *
     * This can be called after the call to upload file. Use the key returned
     * by fileUpload method to request the status of the current upload.
     * Keep calling this API every few seconds  until you get the status value
     * 99 which means that the upload is complete. The quickkey of the file and
     * other related information is also returned along when the upload is
     * complete.
     *
     * @access public
     * @see fileUpload()
     * @param string $uploadKey The upload key returned from method 'fileUpload'
     * @return array|bool Returns an array contains information of the upload process
     */
	public function filePollUpload($uploadKey = NULL)
	{
		if (empty($uploadKey))
			$uploadKey = $this->uploadkey;
		
		return $this->mflib->filePollUpload($this->token, $uploadKey);
	}
	
	/**
     * Adds shared folders to the account
     *
     * @access public
     * @param array|string $folderKey The key that identifies the folder to be attached. You can also specify multiple folderkeys separated by comma or just put them into an array
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function folderAttachForeign($folderKey)
	{
		return $this->mflib->folderAttachForeign($this->token, $folderKey);
	}
	
	/**
     * Creates a new folder
     *
     * @access public
     * @param string $folderName The name of the new folder to be created
     * @param string $parentKey (Optional) The key that identifies an existing folder in which the new folder is to be created. If not specified, the new folder will be created in the root folder (My files)
     * @return array|bool Returns an array contain the quickkey, upload key and created date of the newly created folder; otherwise FALSE if an error occurred
     */
	public function folderCreate($folderName, $parentKey = NULL)
	{
		return $this->mflib->folderCreate($this->token, $folderName, $parentKey);
	}
	
	/**
     * Removes shared folders from the account
     *
     * @access public
     * @param array|string $folderKey The key that identifies the folder to be deattached. You can also specify multiple folderkeys separated by comma or just put them into an array
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function folderDetachForeign($folderKey)
	{
		return $this->mflib->folderDetachForeign($this->token, $folderKey);
	}
	
	/**
     * Deletes a folder
     *
     * @access public
     * @param array|string $folderKey The key that identifies the folder to be moved. You can also specify multiple folderkeys separated by comma or just put them into an array
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function folderDelete($folderKey)
	{
		return $this->mflib->folderDelete($this->token, $folderKey);
	}
	
	/**
     * Returns a folder's immediate sub folders and files.
     *
     * @access public
     * @param string $folderKey (Optional) If folder_key is not passed, the API will return the root folder content (session token is required)
     * @param string $contentType (Optional) Request what type of content. Can be 'folders' or 'files'. Default is 'folders'
     * @param string $orderBy (Optional) Can be 'name', 'created', 'size', 'downloads' (default is 'name'). When requesting folders, only 'name' and 'created' are considered. If 'order_by' is set to anything other than 'name' or 'created' when requesting folders, the output order will default to 'name'
     * @param string $orderDirection (Optional) Order direction. Can be 'asc' or 'desc' (default 'asc')
     * @param int $chunk (Optional) The chunk number starting from 1
     * @return array|bool|null Returns an array contains folder's contents, otherwise FALSE if an error occurred
     */
	public function folderGetContent($folderKey = NULL, $contentType = 'folders', $orderBy = NULL, $orderDirection = NULL, $chunk = NULL)
	{
		return $this->mflib->folderGetContent($folderKey, $this->token, $contentType, $orderBy, $orderDirection, $chunk);
	}
	
	/**
     * Returns information about folder nesting (distance from root)
     *
     * @access public
     * @param array|string $folderKey The key that identifies the folder
     * @return array|bool Returns an array contains folder depth, otherwise FALSE if an error occurred
     */
	public function folderGetDepth($folderKey)
	{
		return $this->mflib->folderGetDepth($this->token, $folderKey);
	}
	
	/**
     * Returns a list of the a folder's details
     *
     * @access public
     * @param array|string $folderKey The key that identifies the folder. You can also specify multiple folderkeys separated by comma or just put them into an array
     * @return array|bool Returns an array contains details of the folder(s)
     */
	public function folderGetInfo($folderKey)
	{
		return $this->mflib->folderGetInfo($folderKey, $this->token);
	}
	
	/**
     * Returns the number indicating the revision of a folder
     *
     * Any changes made to this folder or its content will increment the
     * revision. when the revision resets to 1, the time stamp 'epoch' is
     * updated so both 'revision' and 'epoch' can be used to identify a
     * unique revision
     *
     * @access public
     * @param array|string $folderKey The key that identifies the folder
     * @return array|bool Returns an array contains epoch and revision number for the folder, otherwise FALSE if an error occurred
     */
	public function folderGetRevision($folderKey)
	{
		return $this->mflib->folderGetRevision($folderKey, $this->token);
	}
	
	/**
     * Returns the sibling folders
     *
     * @access public
     * @param string $folderKey The key that identifies the folder
     * @param string $contentFilter (Optional) Can be 'info', 'files','folders', 'content' or 'all' (default 'all'). "content" refers to both files and folders.
     * @param int $start (Optional) Request to return results starting from this number
     * @param int $limit (Optional) The maximum results to be returned
     * @return array|bool|null Returns an array contains folder sibling, otherwise FALSE if an error occurred
     */
	public function folderGetSiblings($folderKey, $contentFilter = 'all', $start = NULL, $limit = NULL)
	{
		return $this->mflib->folderGetSiblings($folderKey, $this->token, $contentFilter, $start, $limit);
	}
	
	/**
     * Moves a folder to another folder. Note: This operation also works with
     * foreign folders
     *
     * @access public
     * @param array|string $folderKeySrc The key that identifies the folder to be moved. You can also specify multiple folderkeys separated by comma or just put them into an array
     * @param string $folderKeyDst (Optional) The key that identifies the destination folder. If omitted, the destination folder will be the root folder (My Files)
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function folderMove($folderKeySrc, $folderKeyDst = NULL)
	{
		return $this->mflib->folderMove($this->token, $folderKeySrc, $folderKeyDst);
	}
	
	/**
     * Searches the content of the given folder
     *
     * @access public
     * @param string $searchText The keywords to search for in filenames, folder names, descriptions and tags
     * @param string $folderKey (Optional) If folder_key is not passed, the API will return the root folder content ($sessionToken is required this point)
     * @return array|bool|null Returns an array contains the search result, otherwise FALSE if an error occurred
     */
	public function folderSearch($searchText, $folderKey = NULL)
	{
		return $this->mflib->folderSearch($searchText, $folderKey, $this->token);
	}
	
	/**
     * Updates a folder's information
     *
     * Example of usage :
     * <code>$mflib->folder_update("wl88kcc0k0xvj", array("description" => "a test folder", "tags" => "test,folder"));</code>
     *
     * @access public
     * @param string $folderKey The quickkey that identifies the folder
     * @param array $information An associative array contains the updated folder information
     * @return array|bool Returns an array contains epoch and revision number, otherwise FALSE if an error occurred
     */
	public function folderUpdate($folderKey, $information)
	{
		return $this->mflib->folderUpdate($this->token, $folderKey, $information);
	}
	
	/**
     * Returns the current API version (major.minor)
     *
     * @access public
     * @return string Returns the current API version
     */
	public function systemVersion()
	{
		return $this->mflib->systemVersion();
	}
	
	/**
     * Returns all the configuration data about the MediaFire system.
     *
     * @access public
     * @return string Returns an array contains the configuration data about the MediaFire system
     */
	public function systemInfo()
	{
		return $this->mflib->systemInfo();
	}
	
	/**
     * Returns the list of all supported document types for preview
     *
     * @access public
     * @param bool $groupByFiletype Whether to group lists by filetype or not
     * @return string Returns an array contains list of all supported document types for preview
     */
	public function systemSupportedMedia($groupByFiletype = FALSE)
	{
		return $this->mflib->systemSupportedMedia($groupByFiletype);
	}
	
	/**
     * Returns the list of all supported documents for editing
     *
     * @access public
     * @param bool $groupByFiletype Whether to group lists by filetype or not
     * @return string Returns an array contains list of all editable document types
     */
	public function systemEditableMedia($groupByFiletype = FALSE)
	{
		return $this->mflib->systemEditableMedia($groupByFiletype);
	}
    
	/**
     * Returns a list of file extensions, their document types and MIME types
     *
     * @access public
     * @return string Returns an array contains the information
     */
	public function systemMimeTypes()
	{
		return $this->mflib->systemMimeTypes();
	}
	
	/**
     * Convert a document/image file in user account
     *
     * @access public
     * @param array|string $quickkey The quickkey that identify the files
     * @param array|string $sizeId The output image resolution
     * @param array|string $saveAs (Optional) Path to the file to which the binary data will be written
     * @param array|string $page (Optional) The document's page to be converted. Default is 'intitial'
     * @param array|string $output (Optional) The output format for document conversion. Default is 'pdf'
     * @return bool|string See below
     */
	public function mediaConversion($quickkey, $saveAs = NULL, $sizeId = '2', $page = 'initial', $output = 'pdf')
	{
		return $this->mflib->mediaConversion($this->token, $quickkey, $saveAs, $sizeId, $page, $output);
	}
	
	/**
     * Returns the HTML format of the MediaFire Terms of Service and its
     * revision, date, whether the user has accepted it not not, and the
     * acceptance token if the user has not accepted the latest terms
     *
     * @access public
     * @return array|bool
     */
	public function userFetchTos()
	{
		return $this->mflib->userFetchTos($this->token);
	}
	
	/**
     * Accept the Terms of Service by sending the acceptance token
     *
     * @access public
     * @return bool Returns TRUE if user agrees to accept the Terms of Service
     */
	public function userAcceptTos()
	{
		return $this->mflib->userFetchTos($this->token, $this->userFetchTos());
	}
	
	/**
     * Returns a list of the user's personal information
     *
     * @access public
     * @param string $singleInfo Instead of returning all of the user's personal information, returns a single value only. Default is NULL
     * @return bool|array|string Returns an array when $singleInfo is NULL, otherwise a string
     */
	public function userGetInfo($singleInfo = NULL)
	{
		return $this->mflib->userGetInfo($this->token, $singleInfo);
	}
	
	/**
     * Returns a fraction number indicating the global revision of 'Myfiles'
     *
     * The revision is in the x.y format. 'x' is the folders-only revision.
     * 'y' is the folders-and-files revision. When the revision resets to 1.0,
     * the time stamp 'epoch' is updated so both 'revision' and 'epoch'
     * can be used to identify a unique revision
     *
     * @access public
     * @return array|bool Returns an array contains epoch and revision number
     */
	public function userMyfilesRevision()
	{
		return $this->mflib->userMyfilesRevision($this->token);
	}
	
	/**
     * Updates the user's personal information
     *
     * Example of usage :
     * <code>$mflib->userUpdate(array("first_name" => "John", "last_name" => "Doe"));</code>
     *
     * @access public
     * @param array $information An associative array contains the updated user's personal information
     * @return bool Returns TRUE on success
     */
	public function userUpdate($information)
	{
		return $this->mflib->userUpdate($this->token, $information);
	}
}
