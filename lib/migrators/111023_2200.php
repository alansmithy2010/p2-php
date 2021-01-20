<?php
/**
 * rep2expack - �o�[�W�����A�b�v���̈ڍs�x��
 */

// {{{ p2_migrate_111023_2200()

/**
 * rev.111023.2200
 *
 * @param array $core_config rep2�R�A�̐ݒ�
 * @param array $user_config �Â����[�U�[�ݒ�
 * @return array �V�������[�U�[�ݒ�
 */
function p2_migrate_111023_2200(array $core_config, array $user_config)
{
    $mobile_device_id_files = array(
        $_conf['auth_imodeid_file'],
        $_conf['auth_docomo_file'],
        $_conf['auth_ez_file'],
        $_conf['auth_jp_file'],
    );
    foreach ($mobile_device_id_files as $filename) {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
    return $user_config;
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: cp932
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: nil
 * End:
 */
// vim: set syn=php fenc=cp932 ai et ts=4 sw=4 sts=4 fdm=marker:
