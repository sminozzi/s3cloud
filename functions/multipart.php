You haven’t highlighted anything yet

When you select text while you’re reading, it'll appear here.
How to upload a File in Multiple Parts Using the PHP SDK Low-Level API
zyxware.com
2 min
August 27, 2021
View Original

The AWS SDK provides API for multipart upload of large files to Amazon S3. We upload large images by part using this API. Multi-part API divides the large object into small objects, uploaded it to amazon independently. After the upload completes it assembles into a single object.

If we need the following requirement you can use low-level API :

    Change sizes of part during the upload.
    Size of the data in unknown in advance.

We need the following procedure to upload files to the amazon bucket.

    Download and install the AWS SDK for PHP.
    Download and install the composer. Each code sample includes the autoload.php like 'require 'vendor/autoload.php'.
    Create an amazon account for credentials.

Lets look on to the sample code.




// Include the AWS SDK using the Composer autoloader.
require 'vendor/autoload.php';

use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\Model\MultipartUpload\UploadBuilder;
use Aws\S3\S3Client;

$aws_access_key = variable_get('aws_access_key', '');
$aws_secret_key = variable_get('aws_secret_key', '');
$bucket = variable_get('aws_bucket_name', '');
$client = S3Client::factory(array(
  'key' => $aws_access_key,
  'secret' => $aws_secret_key,
));

$key = $file_path;
$file_name = $file_path;
$result = $client->createMultipartUpload(array(
  'Bucket' => $bucket,
  'Key'    => $key,
));

$upload_id = $result['UploadId'];

$file = fopen($file_name, 'r');

$parts = array();
$partNumber = 1;

while (!feof($file)) {
  $result = $client->uploadPart(array(
      'Bucket'     => $bucket,
      'Key'        => $key,
      'UploadId'   => $upload_id,
      'PartNumber' => $part_number,
      'Body'       => fread($file, 5 * 1024 * 1024),
  ));
  $parts[] = array(
      'PartNumber' => $part_number++,
      'ETag'       => $result['ETag'],
  );
}
$result = $client->completeMultipartUpload(array(
  'Bucket'   => $bucket,
  'Key'      => $key,
  'UploadId' => $upload_id,
  'Parts'    => $parts,
));

$url = $result['Location'];
fclose($file);


