import React from 'react';
import { Helmet } from 'react-helmet';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import withRouter, { IHasRouter } from '@admin/components/hoc/WithRouter';
import Loader from '@admin/components/Loader';
import EditCommentForm from '@admin/components/blog/comments/EditCommentForm';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

import Comment from '@admin/utils/api/models/Comment';

interface IProps extends IHasRouter<'comment'> {

}

const Edit = withRouter(({ router }: IProps) => {
    const [comment, setComment] = React.useState<Comment>();

    const load = async () => {
        const { params: { comment } } = router;

        const response = await createAuthRequest().get<IComment>(`blog/comments/${comment}`);

        setComment(new Comment(response.data));
    }

    const handleLoadError = async (err: unknown) => {
        const { navigate } = router;

        const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: `An error occurred loading comment: ${message}`,
            confirmButtonText: 'Try Again',
            showConfirmButton: true,
            showCancelButton: true
        });

        if (result.isConfirmed) {
            load();
        } else {
            navigate(-1);
        }
    }

    React.useEffect(() => {
        try {
            load();
        } catch (err) {
            handleLoadError(err);
        }
    }, [router.location.pathname]);

    return (
        <>
            <Helmet>
                <title>Edit Comment</title>
            </Helmet>

            {comment === undefined && <Loader display={{ type: 'over-element' }} />}

            {comment !== undefined && (
                <EditCommentForm comment={comment} setComment={(comment) => setComment(comment)} />
            )}
        </>
    );
});

export default Edit;
